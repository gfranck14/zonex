<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\Paiement;
use FedaPay\FedaPay;
use FedaPay\Customer;
use FedaPay\Transaction;

class FedapayController extends Controller
{
    private $apiKey;
    private $webhookSecret;
    private $baseUrl; // Ajout pour compatibilité

    public function __construct()
    {
        // Configuration du SDK FedaPay
        $apiKey = config('services.fedapay.secret_key');
        $environment = config('services.fedapay.env', 'live');
        
        FedaPay::setApiKey($apiKey);
        FedaPay::setEnvironment($environment);
        
        $this->apiKey = config('services.fedapay.secret_key');
        $this->webhookSecret = config('services.fedapay.webhook_secret');
        
        // Pour compatibilité avec les méthodes REST
        $this->baseUrl = $environment === 'sandbox' ? 
            'https://sandbox-api.fedapay.com' : 
            'https://api.fedapay.com';
        
        Log::info('Configuration FedaPay SDK initialisée', [
            'environment' => $environment,
            'api_key_configured' => !empty($apiKey),
            'api_key_prefix' => $apiKey ? substr($apiKey, 0, 8) . '...' : 'null',
            'base_url' => $this->baseUrl
        ]);
    }

    /**
     * Payer directement sans modal (redirection immédiate)
     */
    public function payDirect(Request $request, $forfaitId)
    {
        try {
            Log::info('=== DÉBUT PAIEMENT DIRECT FEDAPAY SDK ===');
            
            $client = auth('client')->user();
            $forfait = Forfait::findOrFail($forfaitId);
            
            Log::info('Données paiement direct', [
                'forfait_id' => $forfaitId,
                'client_id' => $client->id,
                'forfait_prix' => $forfait->prix,
                'forfait_nom' => $forfait->nom
            ]);
            
            // Vérifier si le forfait a des tickets disponibles (libre ou pending)
            $ticketsDisponibles = Ticket::where('forfaits_id', $forfait->id)
                ->whereIn('statut', ['libre', 'pending'])
                ->count();
                
            if ($ticketsDisponibles === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce forfait n\'a plus de tickets disponibles.'
                ], 400);
            }
            
            // Réserver un ticket en pending AVANT de créer la transaction Fedapay
            $ticket = $this->reserveTicketForPayment($forfait->id, $client->id);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de réserver un ticket. Veuillez réessayer.'
                ], 400);
            }
            
            Log::info('Ticket réservé en pending', [
                'ticket_id' => $ticket->id,
                'username' => $ticket->username,
                'password' => $ticket->password
            ]);
            
            // Créer un paiement local en attente
            Log::info('Création paiement local...');
            $transactionRef = 'TXN_' . time() . '_' . $client->id . '_' . $forfaitId;
            
            // Créer un email et téléphone par défaut pour le client FedaPay
            $defaultEmail = 'client' . $client->id . '@zonex.com';
            $defaultPhone = '';  // Téléphone vide pour que le champ soit vide sur Fedapay
            
            Log::info('Utilisation données par défaut pour client FedaPay', [
                'email' => $defaultEmail,
                'phone' => $defaultPhone,
                'client_id' => $client->id
            ]);
            
            $paiement = Paiement::create([
                'client_id' => $client->id,
                'forfait_id' => $forfait->id,
                'ticket_id' => $ticket->id,  // Associer le ticket réservé
                'montant' => $forfait->prix,
                'telephone' => $defaultPhone,
                'statut' => 'en_attente',
                'reference' => $transactionRef,
                'methode' => 'fedapay',
                'devise' => 'XOF'
            ]);
            Log::info('Paiement local créé', ['paiement_id' => $paiement->id]);
            
            // Créer ou récupérer le client FedaPay avec les données par défaut
            Log::info('Création client FedaPay SDK...');
            $customerId = $this->createOrGetCustomer(
                'Client ' . $client->id,
                $defaultEmail,
                $defaultPhone,
                'BJ'
            );
            
            Log::info('Client FedaPay obtenu', ['customer_id' => $customerId]);
            
            // Créer la transaction via SDK FedaPay
            Log::info('Création transaction FedaPay...');
            
            // URL de callback directe
            $callbackUrl = route('client.fedapay.callback');
            
            $transactionData = $this->createFedaPayTransaction([
                'amount' => (int) $forfait->prix,
                'currency' => 'XOF',
                'description' => "Achat forfait {$forfait->nom} - Zone WiFi",
                'customer_id' => $customerId,
                'callback_url' => $callbackUrl,
                'reference' => $transactionRef,
                'custom_data' => [
                    'client_id' => $client->id,
                    'forfait_id' => $forfait->id,
                    'paiement_id' => $paiement->id
                ]
            ]);
            
            Log::info('Réponse transaction FedaPay SDK', [
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'],
                'transaction_status' => $transactionData['status']
            ]);
            
            if (!$transactionData || !isset($transactionData['id'])) {
                Log::error('Échec création transaction FedaPay SDK');
                // Libérer le ticket car le paiement a échoué
                $this->releaseTicket($ticket->id);
                throw new \Exception('Échec de création de la transaction Fedapay');
            }
            
            // Mettre à jour le paiement avec l'ID de transaction FedaPay
            $paiement->update([
                'fedapay_transaction_id' => $transactionData['id']
            ]);
            
            Log::info('Transaction Fedapay créée avec succès', [
                'transaction_id' => $transactionData['id'],
                'paiement_id' => $paiement->id,
                'ticket_id' => $ticket->id
            ]);
            
            // Générer l'URL de paiement
            $paymentUrl = $this->getPaymentUrl($transactionData['id']);
            
            Log::info('=== PAIEMENT DIRECT FEDAPAY SDK TERMINÉ AVEC SUCCÈS ===');
            
            // Retourner l'URL pour afficher dans un iframe/modal
            return response()->json([
                'success' => true,
                'redirect' => false,
                'payment_url' => $paymentUrl,
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'],
                'paiement_id' => $paiement->id,
                'ticket_id' => $ticket->id,
                'message' => 'Prêt pour le paiement FedaPay'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erreur paiement direct Fedapay : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de paiement : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Afficher la page de paiement
     */
    public function showPaymentForm(Request $request, $forfaitId)
    {
        $client = auth('client')->user();
        $forfait = Forfait::findOrFail($forfaitId);
        
        // Vérifier si le forfait a des tickets disponibles
        $ticketsDisponibles = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre')
            ->count();
            
        if ($ticketsDisponibles === 0) {
            return redirect()->back()->with('error', 'Ce forfait n\'a plus de tickets disponibles.');
        }
        
        return view('PortailClient.payment', compact('client', 'forfait'));
    }
    
    /**
     * Afficher la page de paiement Fedapay SANS numéro de téléphone
     */
    public function showCheckoutWithoutPhone(Request $request, $forfaitId)
    {
        $client = auth('client')->user();
        $forfait = Forfait::findOrFail($forfaitId);
        
        // Vérifier si le forfait a des tickets disponibles
        $ticketsDisponibles = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre')
            ->count();
            
        if ($ticketsDisponibles === 0) {
            return redirect()->back()->with('error', 'Ce forfait n\'a plus de tickets disponibles.');
        }
        
        try {
            // Créer le paiement et la transaction Fedapay
            Log::info('=== CRÉATION PAIEMENT SANS TÉLÉPHONE ===');
            
            $transactionRef = 'TXN_' . time() . '_' . $client->id . '_' . $forfaitId;
            
            // Créer un client Fedapay SANS téléphone
            $defaultEmail = 'client' . $client->id . '_' . time() . '@zonex.com';
            $customerId = $this->createOrGetCustomer(
                'Client ' . $client->id,
                $defaultEmail,
                null,  // Pas de téléphone
                'BJ'
            );
            
            // Créer le paiement local
            $paiement = Paiement::create([
                'client_id' => $client->id,
                'forfait_id' => $forfait->id,
                'montant' => $forfait->prix,
                'telephone' => '',
                'reference' => $transactionRef,
                'statut' => 'en_attente',
                'methode' => 'fedapay',
                'devise' => 'XOF'
            ]);
            
            // Créer la transaction Fedapay
            $callbackUrl = route('client.fedapay.callback');
            
            $transactionData = $this->createFedaPayTransaction([
                'amount' => (int) $forfait->prix,
                'currency' => 'XOF',
                'description' => "Achat forfait {$forfait->nom} - Zone WiFi",
                'customer_id' => $customerId,
                'callback_url' => $callbackUrl,
                'reference' => $transactionRef,
                'custom_data' => [
                    'client_id' => $client->id,
                    'forfait_id' => $forfait->id,
                    'paiement_id' => $paiement->id
                ]
            ]);
            
            // Mettre à jour le paiement
            $paiement->update([
                'fedapay_transaction_id' => $transactionData['id']
            ]);
            
            // Générer l'URL de paiement
            $paymentUrl = $this->getPaymentUrl($transactionData['id']);
            
            // Afficher la vue checkout
            return view('PortaleClient.fedapay-checkout', [
                'transactionId' => $transactionData['id'],
                'paymentUrl' => $paymentUrl,
                'callbackUrl' => $callbackUrl,
                'forfait' => $forfait,
                'client' => $client
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur création checkout sans téléphone: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la préparation du paiement.');
        }
    }
    
    /**
     * Page de succès de paiement
     * Affiche une icône de succès et redirige vers wifi789.net
     */
    public function paymentSuccess(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $message = $request->get('message');
        
        Log::info('Page payment-success affichée', [
            'username' => $username ? substr($username, 0, 4) . '***' : null,
            'password' => $password ? '***' : null,
            'message' => $message
        ]);
        
        return view('PortaleClient.payment-success', [
            'username' => $username,
            'password' => $password,
            'message' => $message
        ]);
    }
    
    /**
     * Initialiser le paiement Fedapay avec REST API
     */
    public function initiatePayment(Request $request, $forfaitId)
    {
        // Vérification manuelle de l'authentification client
        if (!auth('client')->check()) {
            Log::warning('Tentative d\'accès non authentifié à initiatePayment', [
                'forfait_id' => $forfaitId,
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer cette action.'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string|min:8|max:15',
            'email' => 'nullable|email|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()->all()
            ], 422);
        }
        
        $client = auth('client')->user();
        $forfait = Forfait::findOrFail($forfaitId);
        
        // Vérifier à nouveau les tickets disponibles
        $ticketsDisponibles = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre')
            ->count();
            
        if ($ticketsDisponibles === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce forfait n\'a plus de tickets disponibles.'
            ], 400);
        }
        
        try {
            Log::info('=== DÉBUT PAIEMENT FEDAPAY SDK ===');
            Log::info('Configuration FedaPay SDK', [
                'environment' => config('services.fedapay.env', 'live'),
                'app_env' => config('app.env'),
                'ssl_verify_disabled' => config('app.env') === 'local'
            ]);
            
            Log::info('Données reçues', [
                'forfait_id' => $forfaitId,
                'client_id' => $client->id,
                'forfait_prix' => $forfait->prix,
                'forfait_nom' => $forfait->nom
            ]);
            
            // Créer un paiement local en attente
            Log::info('Création paiement local...');
            $transactionRef = 'TXN_' . time() . '_' . $client->id . '_' . $forfaitId;
            
            $paiement = Paiement::create([
                'client_id' => $client->id,
                'forfait_id' => $forfait->id,  // Correction: forfait_id (pas forfaits_id)
                'montant' => $forfait->prix,
                'telephone' => $request->telephone,
                'reference' => $transactionRef,  // Correction: reference (pas transaction_reference)
                'statut' => 'en_attente',
                'methode' => 'fedapay',
                'devise' => 'XOF'
            ]);
            Log::info('Paiement local créé', ['paiement_id' => $paiement->id]);
            
            // Créer la transaction via REST API Fedapay
            Log::info('Création transaction FedaPay...');
            
            // En développement, utiliser l'URL de callback route() si disponible
            // ou une URL de test valide (FedaPay nécessite une URL accessible)
            $callbackUrl = route('client.fedapay.callback');
            
            // Pour les tests en local avec ngrok ou tunnel, utilisez FEDAPAY_CALLBACK_URL
            if (config('app.env') === 'local') {
                $envCallbackUrl = config('services.fedapay.callback_url');
                if ($envCallbackUrl && filter_var($envCallbackUrl, FILTER_VALIDATE_URL)) {
                    $callbackUrl = $envCallbackUrl;
                    Log::info('URL de callback depuis configuration', ['callback_url' => $callbackUrl]);
                } else {
                    Log::warning('Pas de callback URL valide configurée. FedaPay nécessite une URL accessible.', [
                        'configured_url' => $envCallbackUrl,
                        'recommendation' => 'Utilisez ngrok ou configurez FEDAPAY_CALLBACK_URL dans .env'
                    ]);
                }
            }
            
            // Vérifier si nous devons utiliser l'environnement sandbox
            $fedapayEnv = config('services.fedapay.env', 'live');
            if (config('app.env') === 'local' && $fedapayEnv === 'live') {
                Log::warning('Attention: Utilisation de l\'environnement FedaPay LIVE en développement', [
                    'fedapay_env' => $fedapayEnv,
                    'app_env' => config('app.env')
                ]);
            }
            
            $transactionData = $this->createFedaPayTransaction([
                'amount' => (int) $forfait->prix,
                'currency' => 'XOF',
                'description' => "Achat forfait {$forfait->nom} - Zone WiFi",
                'customer_id' => $customerId,
                'callback_url' => $callbackUrl,
                'reference' => $transactionRef,
                'custom_data' => [
                    'client_id' => $client->id,
                    'forfait_id' => $forfait->id,
                    'paiement_id' => $paiement->id
                ]
            ]);
            
            Log::info('Réponse transaction FedaPay SDK', [
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'],
                'transaction_status' => $transactionData['status'],
                'payment_url_available' => !empty($transactionData['payment_url']),
                'url_available' => !empty($transactionData['url'])
            ]);
            
            if (!$transactionData || !isset($transactionData['id'])) {
                Log::error('Échec création transaction FedaPay SDK', [
                    'transaction_data' => $transactionData,
                    'forfait_id' => $forfaitId,
                    'customer_id' => $customerId,
                    'missing_keys' => !isset($transactionData['id']) ? 'id' : 'none'
                ]);
                throw new \Exception('Échec de création de la transaction Fedapay');
            }
            
            // Mettre à jour le paiement avec l'ID de transaction FedaPay
            Log::info('Mise à jour paiement local...', [
                'paiement_id' => $paiement->id,
                'fedapay_transaction_id' => $transactionData['id'],
                'current_statut' => $paiement->statut
            ]);
            
            $updateResult = $paiement->update([
                'fedapay_transaction_id' => $transactionData['id']
            ]);
            
            Log::info('Paiement local mis à jour', [
                'update_success' => $updateResult,
                'fedapay_transaction_id' => $paiement->fedapay_transaction_id,
                'fedapay_status' => $transactionData['status'] ?? 'pending'
            ]);
            
            Log::info("Transaction Fedapay créée avec succès", [
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'],
                'paiement_id' => $paiement->id,
                'client_id' => $client->id,
                'forfait_id' => $forfait->id
            ]);
            
            // Générer l'URL de paiement (utilise l'ID de la transaction pour générer le token)
            Log::info('Génération URL de paiement...', [
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'] ?? null
            ]);
            
            $paymentUrl = $this->getPaymentUrl($transactionData['id']);
            Log::info('URL de paiement générée avec succès', [
                'payment_url' => $paymentUrl,
                'url_length' => strlen($paymentUrl),
                'url_starts_with_http' => strpos($paymentUrl, 'http') === 0
            ]);
            
            Log::info('=== PAIEMENT FEDAPAY SDK TERMINÉ AVEC SUCCÈS ===', [
                'client_id' => $client->id,
                'forfait_id' => $forfait->id,
                'paiement_id' => $paiement->id,
                'transaction_id' => $transactionData['id'],
                'payment_url_generated' => !empty($paymentUrl)
            ]);
            
            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'transaction_id' => $transactionData['id'],
                'transaction_token' => $transactionData['token'],
                'paiement_id' => $paiement->id,
                'message' => 'Redirection vers la page de paiement...'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erreur Fedapay : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de paiement : ' . $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Créer ou récupérer un client Fedapay sans numéro de téléphone
     */
    private function createOrGetCustomer($name, $email, $phoneNumber, $country)
    {
        Log::info('=== RECHERCHE/CRÉATION CLIENT FEDAPAY SDK ===');
        Log::info('Paramètres client', [
            'name' => $name,
            'email' => $email,
            'phone' => $phoneNumber,
            'country' => $country
        ]);
        
        // Générer un email unique basé sur le temps pour éviter les conflits
        $uniqueEmail = !empty($email) ? $email : 'client' . time() . '@zonex.com';
        
        try {
            // Chercher le client par email au lieu du téléphone
            Log::info('Recherche client existant par email...');
            $existingCustomers = Customer::all([
                'per_page' => 100,
                'email' => $uniqueEmail
            ]);
            
            if (count($existingCustomers->customers) > 0) {
                $customer = $existingCustomers->customers[0];
                
                // Vérifier si le nom est vide et le remplacer
                $displayName = !empty($customer->firstname) || !empty($customer->lastname) 
                    ? trim($customer->firstname . ' ' . $customer->lastname) 
                    : 'Client Fedapay';
                
                Log::info("Client existant trouvé sur FedaPay", [
                    'id' => $customer->id,
                    'name' => $displayName,
                    'email' => $customer->email,
                    'phone_number' => $customer->phone_number->number ?? 'non défini',
                    'country' => $customer->phone_number->country ?? 'non défini',
                ]);
                return $customer->id;
            }
            
            // Créer un nouveau client SANS numéro de téléphone
            Log::info('Création nouveau client FedaPay SANS téléphone...');
            $customer = Customer::create([
                'firstname' => $name,
                'lastname' => $name,
                'email' => $uniqueEmail,
                // Ne pas inclure phone_number pour que Fedapay n'affiche pas de numéro
            ]);
            
            Log::info("Nouveau client Fedapay créé avec ID : {$customer->id} (sans téléphone)", [
                'customer_data' => [
                    'id' => $customer->id,
                    'firstname' => $customer->firstname,
                    'lastname' => $customer->lastname,
                    'email' => $customer->email,
                    'phone_number' => 'NON DÉFINI - le client devra saisir son numéro'
                ]
            ]);
            
            return $customer->id;
            
        } catch (\Exception $e) {
            Log::error("Erreur création/recherche client FedaPay SDK: " . $e->getMessage(), [
                'name' => $name,
                'email' => $uniqueEmail,
                'phone' => $phoneNumber,
                'country' => $country,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Impossible de créer/récupérer le client FedaPay: ' . $e->getMessage());
        }
    }
    
    /**
     * Rechercher un client Fedapay par numéro de téléphone
     */
    private function searchCustomer($phoneNumber, $country)
    {
        Log::info('Recherche client FedaPay API', [
            'phone' => $phoneNumber,
            'country' => $country,
            'api_url' => $this->baseUrl . '/v1/customers'
        ]);
        
        try {
            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]);
            
            // Ignorer SSL en développement
            if (config('app.env') === 'local') {
                $http = $http->withoutVerifying();
            }
            
            $response = $http->get($this->baseUrl . '/v1/customers', [
                'phone_number[number]' => $phoneNumber,
                'phone_number[country]' => $country,
            ]);
            
            Log::info('Réponse recherche client', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // FedaPay peut retourner les clients dans différentes structures
                $customers = [];
                if (isset($data['customers'])) {
                    $customers = $data['customers'];
                } elseif (isset($data['customer']['v1/customer'])) {
                    $customers = [$data['customer']['v1/customer']];
                }
                
                Log::info('Clients trouvés', ['count' => count($customers), 'customers' => $customers]);
                return $customers;
            } else {
                Log::warning('Recherche client échouée', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur recherche client Fedapay: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
        }
        
        Log::info('Aucun client trouvé, retour vide');
        return [];
    }
    
    /**
     * Créer un client Fedapay
     */
    private function createCustomer($name, $email, $phoneNumber, $country)
    {
        Log::info('Création client FedaPay API', [
            'name' => $name,
            'email' => $email,
            'phone' => $phoneNumber,
            'country' => $country,
            'api_url' => $this->baseUrl . '/v1/customers'
        ]);
        
        // S'assurer que le nom n'est pas null
        $firstName = $name ?: 'Client';
        $lastName = $name ?: 'ZoneX';
        
        // Essayer avec l'email fourni, puis avec des alternatives si nécessaire
        $emailsToTry = [
            $email,
            'client' . substr($phoneNumber, -4) . '@zonex.com',
            'client' . $phoneNumber . '@zonex.com',
            'client' . time() . '@zonex.com'
        ];
        
        foreach ($emailsToTry as $emailToTry) {
            if (empty($emailToTry)) continue;
            
            $payload = [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $emailToTry,
                'phone_number' => [
                    'number' => $phoneNumber,
                    'country' => $country
                ]
            ];
            
            Log::info('Tentative création client avec email', ['email' => $emailToTry]);
            
            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]);
            
            // Ignorer SSL en développement
            if (config('app.env') === 'local') {
                $http = $http->withoutVerifying();
            }
            
            $response = $http->post($this->baseUrl . '/v1/customers', $payload);
            
            Log::info('Réponse création client', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'email_used' => $emailToTry,
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $customerData = $response->json();
                
                // FedaPay retourne les données dans customer["v1/customer"]
                if (isset($customerData['customer']['v1/customer'])) {
                    $customerData = $customerData['customer']['v1/customer'];
                }
                
                Log::info('Client créé avec succès', ['customer' => $customerData]);
                return $customerData;
            }
            
            // Si l'erreur n'est pas un email dupliqué, ne pas réessayer
            $errorData = null;
            try {
                $errorData = $response->json();
            } catch (\Exception $e) {
                Log::warning('Impossible de décoder la réponse JSON', ['body' => $response->body()]);
            }
            
            // Vérifier si c'est une erreur d'email dupliqué
            $isEmailError = false;
            if ($errorData && isset($errorData['errors']['email'])) {
                $emailErrors = $errorData['errors']['email'];
                if (is_array($emailErrors) && in_array('n\'est pas disponible', $emailErrors)) {
                    $isEmailError = true;
                    Log::info('Email déjà utilisé, essai avec un autre email', ['email' => $emailToTry]);
                }
            }
            
            if (!$isEmailError) {
                // C'est une autre erreur, ne pas réessayer
                Log::error('Échec création client FedaPay (autre erreur)', [
                    'status' => $response->status(),
                    'response' => $errorData,
                    'payload' => $payload,
                    'headers' => $response->headers()
                ]);
                
                $errorMessage = 'Échec de création du client Fedapay';
                if ($errorData && isset($errorData['message'])) {
                    $errorMessage .= ': ' . $errorData['message'];
                } elseif ($response->status() === 500) {
                    $errorMessage .= ' (Erreur serveur FedaPay)';
                }
                
                throw new \Exception($errorMessage);
            }
        }
        
        // Tous les emails ont échoué
        Log::error('Tous les emails ont échoué pour la création du client', [
            'emails_tried' => $emailsToTry
        ]);
        
        throw new \Exception('Impossible de créer un client FedaPay avec un email unique');
    }
    
    /**
     * Créer une transaction Fedapay avec le SDK
     */
    private function createFedaPayTransaction($data)
    {
        Log::info('=== CRÉATION TRANSACTION FEDAPAY SDK ===');
        Log::info('Configuration FedaPay Transaction SDK', [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'description' => $data['description'],
            'customer_id' => $data['customer_id'],
            'callback_url' => $data['callback_url'],
            'reference' => $data['reference'],
            'custom_data' => $data['custom_data']
        ]);
        
        try {
            // Créer la transaction avec le SDK
            // Selon la doc, on utilise customer avec id pour un client existant
            $transaction = Transaction::create([
                'description' => $data['description'],
                'amount' => $data['amount'],
                'currency' => ['iso' => $data['currency']],
                'callback_url' => $data['callback_url'],
                'customer' => ['id' => $data['customer_id']],
                'custom_data' => $data['custom_data']
            ]);
            
            Log::info('Transaction FedaPay SDK créée avec succès', [
                'transaction_id' => $transaction->id,
                'token' => $transaction->token,
                'status' => $transaction->status,
                'payment_url' => $transaction->payment_url ?? null
            ]);
            
            Log::info('=== TRANSACTION FEDAPAY SDK TERMINÉE ===');
            
            // Retourner les données de la transaction
            return [
                'id' => $transaction->id,
                'token' => $transaction->token,
                'status' => $transaction->status,
                'payment_url' => $transaction->payment_url,
                'url' => $transaction->url ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur création transaction FedaPay SDK', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Échec de création de la transaction FedaPay: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtenir l'URL de paiement pour une transaction avec le SDK
     */
    private function getPaymentUrl($transactionId)
    {
        try {
            // Récupérer la transaction
            Log::info('Récupération transaction pour génération token', [
                'transaction_id' => $transactionId
            ]);
            
            $transaction = Transaction::retrieve($transactionId);
            
            // Générer le token pour obtenir l'URL de paiement
            Log::info('Génération token pour URL de paiement', [
                'transaction_id' => $transaction->id
            ]);
            
            $token = $transaction->generateToken();
            
            Log::info('Token généré avec succès', [
                'transaction_id' => $transaction->id,
                'token_url' => $token->url,
                'token_id' => $token->id ?? null
            ]);  
            
            // Ajouter des paramètres pour personnaliser le checkout Fedapay
            // pour cacher le champ téléphone
            $baseUrl = $token->url;
            $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
            $customUrl = $baseUrl . $separator . 'widget=1&data_phone[required]=false';
            
            Log::info('URL de paiement personnalisée', [
                'original_url' => $baseUrl,
                'custom_url' => $customUrl
            ]);
            
            // Retourner l'URL de paiement personnalisée
            return $customUrl;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération URL de paiement', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: construire l'URL manuellement selon la documentation Fedapay
            // L'URL de checkout Fedapay est: baseUrl + /v1/checkout/{token}
            // Pour le fallback sans token, on utilise l'URL de la transaction directement
            $environment = config('services.fedapay.env', 'live');
            $baseUrl = $environment === 'sandbox' ? 
                'https://sandbox.fedapay.com' : 
                'https://pay.fedapay.com';
                
            // Essayer d'obtenir le token via API directement
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()
              ->post($this->baseUrl . '/v1/transactions/' . $transactionId . '/token');
            
            if ($tokenResponse->successful()) {
                $tokenData = $tokenResponse->json();
                $token = $tokenData['token'] ?? $tokenData['v1/token'] ?? null;
                if ($token) {
                    $fallbackUrl = $baseUrl . '/v1/checkout/' . $token;
                } else {
                    $fallbackUrl = $baseUrl . '/transactions/' . $transactionId;
                }
            } else {
                $fallbackUrl = $baseUrl . '/transactions/' . $transactionId;
            }
            
            Log::warning('Utilisation URL fallback', [
                'fallback_url' => $fallbackUrl,
                'environment' => $environment
            ]);
            
            return $fallbackUrl;
        }
    }
    
    /**
     * Callback de retour après paiement
     */
    public function paymentCallback(Request $request)
    {
        Log::info('=== FEDAPAY CALLBACK REÇU ===', [
            'all_params' => $request->all(),
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Le callback Fedapay retourne 'id' et 'status' selon la documentation
        $transactionId = $request->get('id');
        $fedapayStatus = $request->get('status');
        
        Log::info('Fedapay Callback - Paramètres extraits', [
            'transaction_id' => $transactionId,
            'fedapay_status' => $fedapayStatus
        ]);
        
        if (!$transactionId) {
            Log::warning('Fedapay Callback - Pas de transaction ID');
            return redirect()->route('client.shop')->with('error', 'Paiement non trouvé.');
        }
        
        $paiement = Paiement::where('fedapay_transaction_id', $transactionId)->first();
        
        if (!$paiement) {
            Log::warning('Fedapay Callback - Paiement non trouvé', [
                'fedapay_transaction_id' => $transactionId
            ]);
            return redirect()->route('client.shop')->with('error', 'Transaction non trouvée.');
        }
        
        Log::info('Fedapay Callback - Paiement trouvé', [
            'paiement_id' => $paiement->id,
            'paiement_statut' => $paiement->statut,
            'ticket_id' => $paiement->ticket_id
        ]);
        
        // Stocker le statut original envoyé par JavaScript (avant vérification API)
        $originalStatus = $request->get('status', '');
        $userCanceled = in_array($originalStatus, ['canceled', 'closed', 'cancelled', 'exit']);
        
        Log::info('Fedapay Callback - Statut original JS et détection annulation', [
            'original_status' => $originalStatus,
            'user_canceled' => $userCanceled
        ]);
        
        // Vérifier le statut final avec l'API Fedapay
        $fedapayStatus = $this->verifyPaymentStatus($paiement);
        
        Log::info('Fedapay Callback - Statut après vérification API', [
            'paiement_id' => $paiement->id,
            'fedapay_status' => $fedapayStatus,
            'user_canceled' => $userCanceled,
            'statut' => $paiement->statut
        ]);
        
        // Si l'API retourne 'pending' et l'utilisateur a annulé, traiter comme annulé
        if ($fedapayStatus === 'pending' && $userCanceled) {
            $fedapayStatus = 'canceled';
            Log::info('Fedapay Callback - Statut ajusté de pending à canceled (utilisateur a annulé)');
        }
        
        // Gérer selon le statut du paiement
        switch ($fedapayStatus) {
            case 'approved':
                // Paiement réussi
                $paiement->update(['statut' => 'reussi']);
                
                Log::info('Fedapay Callback - Paiement réussi', [
                    'paiement_id' => $paiement->id,
                    'ticket_id' => $paiement->ticket_id
                ]);
                
                // Marquer le ticket comme vendu
                if ($paiement->ticket_id) {
                    Ticket::where('id', $paiement->ticket_id)->update([
                        'statut' => 'vendu',
                        'client_id' => $paiement->client_id,
                        'date_vente' => now(),
                        'prix_achat' => $paiement->montant,
                    ]);
                    
                    // Mettre à jour les dépenses du client
                    $client = Client::find($paiement->client_id);
                    $client->increment('total_depense', $paiement->montant);
                    
                    // Récupérer les infos du ticket pour la redirection
                    $ticket = Ticket::with('forfait')->find($paiement->ticket_id);
                    
                    Log::info('Fedapay Callback - Ticket vendu', [
                        'username' => $ticket->username,
                        'password' => $ticket->password,
                        'forfait_id' => $ticket->forfait->id ?? $paiement->forfait_id
                    ]);
                    
                    // Obtenir le forfait_id depuis le ticket
                    $forfaitId = $ticket->forfait->id ?? $paiement->forfait_id;
                    
                    // Si pas de forfait_id, rediriger vers la page de succès
                    if (!$forfaitId) {
                        Log::warning('Fedapay Callback - Pas de forfait_id trouvé, redirection vers page de succès', [
                            'paiement_id' => $paiement->id,
                            'ticket_id' => $paiement->ticket_id
                        ]);
                        $successUrl = route('client.fedapay.payment.success')
                            . '?status=success'
                            . '&username=' . urlencode($ticket->username)
                            . '&password=' . urlencode($ticket->password)
                            . '&message=' . urlencode('Paiement réussi !');
                        return redirect($successUrl);
                    }
                    
                    // Rediriger vers la page de succès de paiement
                    $successUrl = route('client.fedapay.payment.success')
                        . '?status=success'
                        . '&username=' . urlencode($ticket->username)
                        . '&password=' . urlencode($ticket->password)
                        . '&message=' . urlencode('Paiement réussi ! Vos identifiants WiFi:');
                    
                    return redirect($successUrl);
                }
                
                return redirect()->route('client.ticket', ['ticket' => $paiement->ticket_id])
                    ->with('success', 'Paiement réussi ! Votre ticket est disponible.');
                
            case 'declined':
                // Paiement décliné
                $paiement->update(['statut' => 'echoue']);
                
                Log::warning('Fedapay Callback - Paiement décliné', [
                    'paiement_id' => $paiement->id
                ]);
                
                // Libérer le ticket (retour à libre)
                $this->releaseTicket($paiement->ticket_id);
                
                // Obtenir le forfait_id pour la redirection
                $forfaitId = null;
                if ($paiement->ticket_id) {
                    $ticket = Ticket::with('forfait')->find($paiement->ticket_id);
                    $forfaitId = $ticket->forfait->id ?? $paiement->forfait_id;
                } else {
                    $forfaitId = $paiement->forfait_id;
                }
                
                // Si pas de forfait_id, rediriger vers la page de succès
                if (!$forfaitId) {
                    Log::warning('Fedapay Callback - Pas de forfait_id trouvé (declined), redirection vers page de succès', [
                        'paiement_id' => $paiement->id
                    ]);
                    $errorUrl = route('client.fedapay.payment.success')
                        . '?status=failed'
                        . '&message=' . urlencode('Le paiement a été décliné. Veuillez réessayer.');
                    return redirect($errorUrl);
                }
                
                // Rediriger vers la page de succès avec message d'erreur
                $errorUrl = route('client.fedapay.payment.success')
                    . '?status=failed'
                    . '&message=' . urlencode('Le paiement a été décliné. Veuillez réessayer.');
                
                return redirect($errorUrl);
                
            case 'canceled':
                // Paiement annulé par l'utilisateur
                $paiement->update(['statut' => 'annule']);
                
                Log::info('Fedapay Callback - Paiement annulé', [
                    'paiement_id' => $paiement->id
                ]);
                
                // Libérer le ticket (retour à libre)
                $this->releaseTicket($paiement->ticket_id);
                
                // Obtenir le forfait_id pour la redirection
                $forfaitId = null;
                if ($paiement->ticket_id) {
                    $ticket = Ticket::with('forfait')->find($paiement->ticket_id);
                    $forfaitId = $ticket->forfait->id ?? $paiement->forfait_id;
                } else {
                    $forfaitId = $paiement->forfait_id;
                }
                
                // Si pas de forfait_id, rediriger vers la page de succès
                if (!$forfaitId) {
                    Log::warning('Fedapay Callback - Pas de forfait_id trouvé (canceled), redirection vers page de succès', [
                        'paiement_id' => $paiement->id
                    ]);
                    $cancelUrl = route('client.fedapay.payment.success')
                        . '?status=canceled'
                        . '&message=' . urlencode('Le paiement a été annulé. Vous pouvez réessayer.');
                    return redirect($cancelUrl);
                }
                
                // Rediriger vers la page de succès avec message d'annulation
                $cancelUrl = route('client.fedapay.payment.success')
                    . '?status=canceled'
                    . '&message=' . urlencode('Le paiement a été annulé. Vous pouvez réessayer.');
                
                return redirect($cancelUrl);
                
            default:
                // Statut inconnu ou non géré
                Log::warning('Fedapay Callback - Statut inconnu', [
                    'paiement_id' => $paiement->id,
                    'fedapay_status' => $fedapayStatus ?? 'non fourni',
                    'user_canceled' => $userCanceled
                ]);
                
                // Si l'utilisateur a annulé, traiter comme annulé même si le statut API est inconnu
                if ($userCanceled) {
                    $paiement->update(['statut' => 'annule']);
                    $this->releaseTicket($paiement->ticket_id);
                    
                    $forfaitId = $paiement->forfait_id;
                    $cancelUrl = route('client.fedapay.payment.success')
                        . '?status=canceled'
                        . '&forfait_id=' . $forfaitId
                        . '&message=' . urlencode('Le paiement a été annulé. Vous pouvez réessayer.');
                    
                    Log::info('Fedapay Callback - Statut inconnu traité comme annulé (utilisateur a fermé la fenêtre)');
                    
                    return redirect($cancelUrl);
                }
                
                return redirect()->route('client.shop')->with('error', 
                    'Le statut du paiement est inconnu. Veuillez contacter le support.');
        }
    }
    
    /**
     * Webhook pour les notifications Fedapay
     */
    public function webhook(Request $request)
    {
        Log::info('Fedapay Webhook', $request->all());
        
        // Vérifier la signature du webhook pour la sécurité
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('Signature webhook invalide');
            return response()->json(['status' => 'error', 'message' => 'Signature invalide'], 401);
        }
        
        // Le webhook Fedapay peut retourner 'transaction_id' ou 'id'
        $transactionId = $request->get('transaction_id') ?? $request->get('id');
        $fedapayStatus = $request->get('status');
        
        if (!$transactionId) {
            return response()->json(['status' => 'error'], 400);
        }
        
        $paiement = Paiement::where('fedapay_transaction_id', $transactionId)->first();
        
        if (!$paiement) {
            return response()->json(['status' => 'error'], 404);
        }
        
        // Mettre à jour le statut du paiement
        $fedapayStatus = $this->verifyPaymentStatus($paiement);
        
        Log::info('Fedapay Webhook - Statut mis à jour', [
            'paiement_id' => $paiement->id,
            'fedapay_status' => $fedapayStatus
        ]);
        
        // Gérer selon le statut
        switch ($fedapayStatus) {
            case 'approved':
                $paiement->update(['statut' => 'reussi']);
                
                // Marquer le ticket comme vendu
                if ($paiement->ticket_id) {
                    Ticket::where('id', $paiement->ticket_id)->update([
                        'statut' => 'vendu',
                        'client_id' => $paiement->client_id,
                        'date_vente' => now(),
                        'prix_achat' => $paiement->montant,
                    ]);
                    
                    // Mettre à jour les dépenses du client
                    $client = Client::find($paiement->client_id);
                    $client->increment('total_depense', $paiement->montant);
                }
                break;
                
            case 'declined':
                $paiement->update(['statut' => 'echoue']);
                $this->releaseTicket($paiement->ticket_id);
                break;
                
            case 'canceled':
                $paiement->update(['statut' => 'annule']);
                $this->releaseTicket($paiement->ticket_id);
                break;
        }
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Vérifier la signature du webhook Fedapay
     */
    private function verifyWebhookSignature(Request $request)
    {
        if (empty($this->webhookSecret)) {
            // Si pas de secret configuré, accepter en développement
            if (config('app.env') === 'local') {
                return true;
            }
            return false;
        }
        
        $signature = $request->header('X-Fedapay-Signature');
        if (!$signature) {
            $signature = $request->header('X-Hub-Signature-256');
        }
        
        if (!$signature) {
            return false;
        }
        
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }
    
    /**
     * Vérifier le statut du paiement avec l'API Fedapay
     */
    private function verifyPaymentStatus($paiement)
    {
        if (!$paiement->fedapay_transaction_id) {
            return;
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ]);
        
        // Ignorer SSL en développement
        if (config('app.env') === 'local') {
            $response = $response->withoutVerifying();
        }
        
        $response = $response->get($this->baseUrl . '/v1/transactions/' . $paiement->fedapay_transaction_id);
        
        if ($response->successful()) {
            $statusData = $response->json();
            
            Log::info('Fedapay API Response - Vérification statut', [
                'transaction_id' => $paiement->fedapay_transaction_id,
                'raw_response_keys' => array_keys($statusData)
            ]);
            
            // Fedapay retourne la réponse dans 'v1/transaction'
            // Structure: {"v1/transaction": {"status": "approved", ...}}
            $transactionData = $statusData['v1/transaction'] ?? $statusData;
            $status = $transactionData['status'] ?? 'unknown';
            
            Log::info('Statut extrait de la réponse Fedapay', [
                'status' => $status,
                'transaction_id' => $paiement->fedapay_transaction_id
            ]);
            
            // Fedapay utilise: approved, declined, canceled, pending, transferred, refunded
            $statutMap = [
                'approved' => 'reussi',
                'declined' => 'echoue',
                'canceled' => 'annule',
                'pending' => 'en_attente',
                'transferred' => 'reussi',
                'refunded' => 'annule'
            ];
            
            $mappedStatus = $statutMap[$status] ?? 'echoue';
            
            $paiement->update([
                'statut' => $mappedStatus
            ]);
            
            Log::info('Statut paiement mis à jour', [
                'transaction_id' => $paiement->fedapay_transaction_id,
                'fedapay_status' => $status,
                'local_status' => $mappedStatus
            ]);
            
            return $status;
        } else {
            Log::error('Erreur lors de la vérification du statut Fedapay', [
                'transaction_id' => $paiement->fedapay_transaction_id,
                'response' => $response->body()
            ]);
            return null;
        }
    }
    
    /**
     * Réserver un ticket pour un paiement en cours
     * Le ticket passe de 'libre' à 'pending' pour éviter les conflits
     */
    private function reserveTicketForPayment($forfaitId, $clientId)
    {
        Log::info('Réservation ticket pour paiement', [
            'forfait_id' => $forfaitId,
            'client_id' => $clientId
        ]);
        
        // Chercher un ticket libre
        $ticket = Ticket::where('forfaits_id', $forfaitId)
            ->where('statut', 'libre')
            ->lockForUpdate()
            ->first();
        
        if (!$ticket) {
            Log::warning('Aucun ticket libre trouvé', ['forfait_id' => $forfaitId]);
            return null;
        }
        
        // Réserver le ticket en le passant en pending
        $ticket->update([
            'statut' => 'pending',
            'client_id' => $clientId
        ]);
        
        Log::info('Ticket réservé avec succès', [
            'ticket_id' => $ticket->id,
            'username' => $ticket->username,
            'statut' => 'pending'
        ]);
        
        return $ticket;
    }
    
    /**
     * Libérer un ticket (retour à 'libre') suite à un paiement échoué/annulé
     */
    private function releaseTicket($ticketId)
    {
        if (!$ticketId) {
            return;
        }
        
        Log::info('Libération du ticket', ['ticket_id' => $ticketId]);
        
        $ticket = Ticket::find($ticketId);
        
        if ($ticket && $ticket->statut === 'pending') {
            $ticket->update([
                'statut' => 'libre',
                'client_id' => null,
                'date_vente' => null,
                'paiement_id' => null
            ]);
            
            Log::info('Ticket libéré avec succès', [
                'ticket_id' => $ticket->id,
                'nouveau_statut' => 'libre'
            ]);
        }
    }
    
    /**
     * Créer et assigner un ticket au client
     */
    private function createAndAssignTicket($paiement)
    {
        // Chercher un ticket libre existant
        $ticket = Ticket::where('forfaits_id', $paiement->forfait_id)
            ->where('statut', 'libre')
            ->first();
        
        // Si pas de ticket, en créer un nouveau
        if (!$ticket) {
            $forfait = Forfait::find($paiement->forfait_id);
            $ticket = Ticket::create([
                'forfaits_id' => $forfait->id,
                'username' => 'TXN_' . strtoupper(substr(md5(time()), 0, 6)),
                'password' => rand(1000, 9999),
                'statut' => 'libre',
                'date_vente' => null,
            ]);
        }
        
        // Marquer le ticket comme vendu et l'assigner
        // Utiliser le montant du paiement comme prix d'achat
        $ticket->update([
            'statut' => 'vendu',
            'client_id' => $paiement->client_id,
            'date_vente' => now(),
            'paiement_id' => $paiement->id,
            'prix_achat' => $paiement->montant,
        ]);
        
        // Mettre à jour le paiement avec l'ID du ticket
        $paiement->update(['ticket_id' => $ticket->id]);
        
        // Mettre à jour les dépenses du client
        $client = Client::find($paiement->client_id);
        $client->increment('total_depense', $paiement->montant);
    }
}
