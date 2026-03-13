<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\WifiZone;
use App\Models\BlockedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientPortalController extends Controller
{
    /**
     * Page d'accueil / Landing (Login & Register) - Token obligatoire
     */
    public function landing(Request $request, $token)
    {
        Log::info('ClientPortalController::landing - Route atteinte');
        Log::info('ClientPortalController::landing - Request URI: ' . $request->getRequestUri());
        Log::info('ClientPortalController::landing - Token: ' . $token);
        Log::info('ClientPortalController::landing - Auth client: ' . (Auth::guard('client')->check() ? 'Connecté' : 'Déconnecté'));
        Log::info('ClientPortalController::landing - Auth proprio: ' . (Auth::guard('proprio')->check() ? 'Connecté' : 'Déconnecté'));
        
        // Le token est obligatoire - chercher la zone WiFi via le token
        $wifizone = \App\Models\WifiZone::where('token', $token)->first();
        
        if (!$wifizone) {
            // Si le token n'est pas valide, rediriger vers une page d'erreur
            abort(404, 'Token invalide ou expiré. Veuillez contacter le support.');
        }
        
        // Stocker la zone et le token en session
        session(['zone_id' => $wifizone->id]);
        session(['wifizone_token' => $token]);

        // Récupérer MAC address (depuis URL Mikrotik)
        $mac = $request->get('mac_address') ?? $request->get('mac');

        return view('PortailClient.landing', compact('wifizone', 'mac', 'token'));
    }

    /**
     * Traitement de l'inscription
     */
    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'pseudo' => 'required|string|max:255',
            'telephone' => 'required|string|unique:clients,telephone', // Simplifié pour démo
            'password' => 'required|string|min:4|confirmed',
            'mac' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()->all()
                ], 422);
            }
            return back()->withErrors($validator)->onlyInput('telephone', 'pseudo');
        }

        $client = Client::create([
            'pseudo' => $request->pseudo,
            'nom_complet' => $request->pseudo, // Fallback
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'mac_address' => $request->mac,
            'derniere_zone' => session('zone_id'),
            'total_depense' => 0,
        ]);

        // Auto login
        Auth::guard('client')->login($client);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Compte créé avec succès !',
                'redirect' => route('client.shop')
            ]);
        }

        return redirect()->route('client.shop');
    }

    /**
     * Traitement du login avec vérification des tickets et logout_cause
     */
    public function login(Request $request)
    {
        Log::info('🔍 DÉBUT DU PROCESSUS DE LOGIN - PORTAIL CAPTIF', [
            'timestamp' => now()->toISOString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'route' => $request->route() ? $request->route()->getName() : 'NO_ROUTE',
            'token' => request()->route('token')
        ]);

        // Validation des données
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('❌ VALIDATION ÉCHOUÉE - LOGIN', [
                'errors' => $validator->errors()->toArray(),
                'input_telephone' => $request->telephone,
                'ip' => $request->ip()
            ]);
            return back()->withErrors(['login' => 'Veuillez remplir tous les champs correctement']);
        }

        $telephone = $request->telephone;
        $password = $request->password;

        // Normalisation du numéro de téléphone
        $telephoneInput = preg_replace('/[^0-9]/', '', $telephone);
        
        Log::info('📱 NUMÉRO DE TÉLÉPHONE NORMALISÉ', [
            'telephone_original' => $telephone,
            'telephone_normalise' => $telephoneInput
        ]);

        // Recherche du client
        $client = Client::where(function($query) use ($telephoneInput) {
            $query->where('telephone', $telephoneInput)
                  ->orWhere('telephone', '+' . $telephoneInput)
                  ->orWhere('telephone', '00225' . $telephoneInput);
        })->first();

        Log::info('� RÉSULTAT RECHERCHE CLIENT', [
            'client_trouve' => $client ? true : false,
            'client_id' => $client?->id,
            'client_pseudo' => $client?->pseudo,
            'client_telephone' => $client?->telephone,
            'telephone_recherche' => $telephoneInput
        ]);

        // Vérifier si le client existe et si le mot de passe est correct
        if ($client && Hash::check($password, $client->password)) {
            Log::info('✅ MOT DE PASSE CORRECT - CLIENT AUTHENTIFIÉ', [
                'client_id' => $client->id,
                'client_pseudo' => $client->pseudo,
                'timestamp' => now()->toISOString()
            ]);
            
            // Récupérer le token de la wifizone depuis l'URL actuelle
            $token = request()->route('token');
            
            Log::info('🔍 TOKEN DE ZONE RÉCUPÉRÉ', [
                'token' => $token,
                'token_present' => $token ? 'OUI' : 'NON'
            ]);
            
            if (!$token) {
                Log::error('❌ TOKEN DE ZONE NON TROUVÉ - ERREUR CRITIQUE', [
                    'client_id' => $client->id,
                    'ip' => $request->ip(),
                    'timestamp' => now()->toISOString()
                ]);
                return back()->withErrors(['login' => 'Token de zone non trouvé']);
            }
            
            // Trouver la wifizone via le token
            $wifizone = \App\Models\WifiZone::where('token', $token)->first();
            
            Log::info('� ZONE TROUVÉE', [
                'zone_trouvee' => $wifizone ? true : false,
                'zone_id' => $wifizone?->id,
                'zone_nom' => $wifizone?->nom_zone,
                'zone_hotspot_address' => $wifizone?->hotspot_address,
                'token_recherche' => $token
            ]);
            
            if (!$wifizone) {
                Log::error('❌ ZONE NON VALIDE - ERREUR CRITIQUE', [
                    'token' => $token,
                    'client_id' => $client->id,
                    'ip' => $request->ip(),
                    'timestamp' => now()->toISOString()
                ]);
                return back()->withErrors(['login' => 'Zone non valide']);
            }
            
            // Récupérer les IDs des forfaits de cette zone
            $zoneForfaitIds = \App\Models\Forfait::where('wifizones_id', $wifizone->id)->pluck('id');
            
            Log::info('🎫 FORFAITS DE LA ZONE', [
                'zone_id' => $wifizone->id,
                'forfait_ids' => $zoneForfaitIds->toArray(),
                'nombre_forfaits' => $zoneForfaitIds->count()
            ]);
            
            // Vérifier si le client a un ticket vendu dans cette zone (le plus récent)
            $ticket = \App\Models\Ticket::where('client_id', $client->id)
                ->whereIn('forfaits_id', $zoneForfaitIds)
                ->where('statut', 'vendu')
                ->orderBy('date_vente', 'desc')
                ->first();
            
            Log::info('🎫 TICKET LE PLUS RÉCENT TROUVÉ', [
                'ticket_trouve' => $ticket ? true : false,
                'ticket_id' => $ticket?->id,
                'ticket_statut' => $ticket?->statut,
                'ticket_username' => $ticket?->username,
                'ticket_logout_cause' => $ticket?->logout_cause,
                'ticket_created_at' => $ticket?->created_at,
                'client_id' => $client->id,
                'zone_id' => $wifizone->id
            ]);
            
            // Si pas de ticket, afficher le shop
            if (!$ticket) {
                Log::info('📋 AUCUN TICKET TROUVÉ - AFFICHAGE DU SHOP', [
                    'client_id' => $client->id,
                    'client_telephone' => $client->telephone,
                    'zone_id' => $wifizone->id,
                    'zone_nom' => $wifizone->nom_zone,
                    'action' => 'AFFICHER_SHOP',
                    'raison' => 'AUCUN_TICKET_VENDU',
                    'timestamp' => now()->toISOString()
                ]);
                
                // Connecter le client et afficher le shop
                Auth::guard('client')->login($client);
                $request->session()->regenerate();
                
                $client->update(['derniere_zone' => $wifizone->id]);
                session(['zone_id' => $wifizone->id]);
                session(['wifizone_token' => $token]);
                
                Log::info('✅ CLIENT CONNECTÉ SANS TICKET - SESSION CRÉÉE', [
                    'client_id' => $client->id,
                    'zone_id' => $wifizone->id,
                    'session_created' => true,
                    'redirect_to' => 'SHOP',
                    'timestamp' => now()->toISOString()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Connecté - Aucun ticket trouvé',
                        'redirect' => route('client.shop'),
                        'action' => 'SHOW_SHOP',
                        'reason' => 'NO_TICKET'
                    ]);
                }
                
                return redirect()->route('client.shop');
            }
            
            // Analyser le logout_cause
            $logoutCause = $ticket->logout_cause;
            Log::info('🔍 ANALYSE DU LOGOUT_CAUSE', [
                'ticket_id' => $ticket->id,
                'logout_cause' => $logoutCause,
                'logout_cause_null' => is_null($logoutCause),
                'client_id' => $client->id,
                'zone_id' => $wifizone->id,
                'timestamp' => now()->toISOString()
            ]);
            
            // Cas 1: user request ou keepalive timeout -> reconnexion automatique
            if (in_array($logoutCause, ['user request', 'keepalive timeout'])) {
                Log::info('🔄 DÉCISION: RECONNEXION WIFI AUTOMATIQUE', [
                    'logout_cause' => $logoutCause,
                    'ticket_id' => $ticket->id,
                    'ticket_username' => $ticket->username,
                    'ticket_password' => $ticket->password,
                    'client_id' => $client->id,
                    'zone_id' => $wifizone->id,
                    'action' => 'REDIRECTION_WIFI',
                    'wifi_url' => 'http://wifi789.net/login?username=' . $ticket->username . '&password=' . $ticket->password,
                    'timestamp' => now()->toISOString()
                ]);
                
                // Connecter le client
                Auth::guard('client')->login($client);
                $request->session()->regenerate();
                
                $client->update(['derniere_zone' => $wifizone->id]);
                session(['zone_id' => $wifizone->id]);
                session(['wifizone_token' => $token]);
                
                $ticket->update([
                   
                    'logout_cause' => null
                ]);
                
                Log::info('🎫 TICKET MIS À JOUR POUR RECONNEXION', [
                    'ticket_id' => $ticket->id,
                   
                   
                    'logout_cause_reset' => true,
                    'timestamp' => now()->toISOString()
                ]);
                
                // Rediriger vers la connexion WiFi
                $redirectUrl = "http://wifi789.net/login?username=" . urlencode($ticket->username) . "&password=" . urlencode($ticket->password);
                
                Log::info('🌐 REDIRECTION VERS LA CONNEXION WIFI', [
                    'redirect_url' => $redirectUrl,
                    'action' => 'REDIRECT_TO_WIFI',
                    'client_id' => $client->id,
                    'ticket_id' => $ticket->id,
                    'timestamp' => now()->toISOString()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Reconnexion automatique en cours...',
                        'redirect' => $redirectUrl,
                        'wifi_redirect' => true,
                        'action' => 'WIFI_RECONNECT',
                        'logout_cause' => $logoutCause
                    ]);
                }
                
                return redirect($redirectUrl);
            }
            
            // Cas 2: session timeout -> afficher le shop
            if ($logoutCause === 'session timeout') {
                Log::info('⏰ DÉCISION: SESSION TIMEOUT - AFFICHAGE SHOP', [
                    'logout_cause' => $logoutCause,
                    'ticket_id' => $ticket->id,
                    'client_id' => $client->id,
                    'zone_id' => $wifizone->id,
                    'action' => 'SHOW_SHOP_SESSION_TIMEOUT',
                    'timestamp' => now()->toISOString()
                ]);
                
                // Connecter le client et afficher le shop
                Auth::guard('client')->login($client);
                $request->session()->regenerate();
                
                $client->update(['derniere_zone' => $wifizone->id]);
                session(['zone_id' => $wifizone->id]);
                session(['wifizone_token' => $token]);
                
                Log::info('✅ CLIENT CONNECTÉ APRÈS SESSION TIMEOUT', [
                    'client_id' => $client->id,
                    'zone_id' => $wifizone->id,
                    'session_created' => true,
                    'redirect_to' => 'SHOP_SESSION_TIMEOUT',
                    'timestamp' => now()->toISOString()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Session expirée - Veuillez recharger',
                        'redirect' => route('client.shop'),
                        'action' => 'SHOW_SHOP',
                        'reason' => 'SESSION_TIMEOUT',
                        'logout_cause' => $logoutCause
                    ]);
                }
                
                return redirect()->route('client.shop')->with('info', 'Votre session a expiré. Veuillez recharger votre compte.');
            }
            
            // Cas3: Autre cause -> afficher le shop par défaut
            Log::info('🔄 DÉCISION: AUTRE CAUSE - AFFICHAGE SHOP PAR DÉFAUT', [
                'logout_cause' => $logoutCause,
                'ticket_id' => $ticket->id,
                'client_id' => $client->id,
                'zone_id' => $wifizone->id,
                'action' => 'SHOW_SHOP_DEFAULT',
                'timestamp' => now()->toISOString()
            ]);
            
            // Connecter le client et afficher le shop
            Auth::guard('client')->login($client);
            $request->session()->regenerate();
            
            $client->update(['derniere_zone' => $wifizone->id]);
            session(['zone_id' => $wifizone->id]);
            session(['wifizone_token' => $token]);
            
            Log::info('✅ CLIENT CONNECTÉ - CAS PAR DÉFAUT', [
                'client_id' => $client->id,
                'zone_id' => $wifizone->id,
                'session_created' => true,
                'redirect_to' => 'SHOP_DEFAULT',
                'logout_cause' => $logoutCause,
                'timestamp' => now()->toISOString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connecté',
                    'redirect' => route('client.shop'),
                    'action' => 'SHOW_SHOP',
                    'reason' => 'DEFAULT_CASE',
                    'logout_cause' => $logoutCause
                ]);
            }
            
            return redirect()->route('client.shop');
        }

        // Si les identifiants sont incorrects
        Log::warning('❌ IDENTIFIANTS INCORRECTS - LOGIN ÉCHOUÉ', [
            'telephone' => $telephone,
            'telephone_normalise' => $telephoneInput,
            'client_trouve' => $client ? true : false,
            'client_id' => $client?->id,
            'mot_de_passe_correct' => $client ? Hash::check($password, $client->password) : false,
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);
        
        return back()->withErrors(['login' => 'Identifiants incorrects']);
    }

    /**
     * Affiche le formulaire de réinitialisation de mot de passe
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $telephone = $request->get('telephone');
        
        // Vérifier que le token est valide
        $resetRecord = \Illuminate\Support\Facades\DB::table('client_password_resets')
            ->where('telephone', $telephone)
            ->where('token', hash('sha256', $token))
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
        
        if (!$resetRecord) {
            return redirect()->route('client.landing')->with('error', 'Lien de réinitialisation invalide ou expiré.');
        }
        
        return view('PortailClient.reset-password', [
            'token' => $token,
            'telephone' => $telephone
        ]);
    }

    /**
     * Traite la réinitialisation du mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'telephone' => 'required|string',
            'password' => 'required|string|min:4|confirmed',
        ]);
        
        $client = Client::where('telephone', $request->telephone)->first();
        if (!$client) {
            return back()->withErrors(['telephone' => 'Client non trouvé.']);
        }

        $client->update([
            'password' => Hash::make($request->password)
        ]);

        // Supprimer le token de réinitialisation
        \Illuminate\Support\Facades\DB::table('client_password_resets')
            ->where('telephone', $request->telephone)
            ->delete();

        return redirect()->route('client.landing')->with('success', 'Mot de passe réinitialisé avec succès.');
    }

    /**
     * Page boutique (après connexion)
     */
    public function shop(Request $request)
    {
        $client = auth('client')->user();
        $zoneId = session('zone_id');
        
        if (!$zoneId) {
            return redirect()->route('client.landing')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }
        
        $wifizone = \App\Models\WifiZone::find($zoneId);
        if (!$wifizone) {
            return redirect()->route('client.landing')->with('error', 'Zone non trouvée. Veuillez vous reconnecter.');
        }
        
        // Récupérer les forfaits de cette zone avec vérification des tickets disponibles
        $forfaits = \App\Models\Forfait::where('wifizones_id', $wifizone->id)
            ->withCount(['tickets' => function($query) {
                $query->where('statut', 'libre');
            }])
            ->get();
        
        $wifizoneToken = session('wifizone_token');
        
        return view('PortailClient.shop', compact('client', 'wifizone', 'forfaits', 'wifizoneToken'));
    }

    /**
     * Achat fictif (Simulation)
     */
    public function buy($forfaitId)
    {
        $client = Auth::guard('client')->user();
        $forfait = Forfait::findOrFail($forfaitId);

        // 1. Chercher un ticket libre existant
        $ticket = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre') // Assumant 'libre' comme statut dispo
            ->first();

        // 2. Si pas de ticket disponible, afficher un message d'erreur
        if (!$ticket) {
            return back()->with('error', 'Désolé, il n\'y a plus de tickets disponibles pour ce forfait. Veuillez contacter l\'administrateur.');
        }

        // 3. "Vente" du ticket
        $ticket->update([
            'statut' => 'vendu',
            'client_id' => $client->id,
            'date_vente' => now(),
            'prix_achat' => $forfait->prix,
        ]);

        // MAJ Dépense Client
        $client->increment('total_depense', $forfait->prix);

        // 4. Redirection vers la livraison
        return redirect()->route('client.ticket', ['ticket' => $ticket->id]);
    }

    /**
     * Livraison du Ticket
     */
    public function ticket(Ticket $ticket)
    {
        // Vérification sécurité : le ticket appartient bien au client connecté
        if ($ticket->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        $wifizone = $ticket->forfait->wifizone;
        // Adresse IP Mikrotik typique ou depuis la zone
        // TODO: Ajouter champ mikrotik_ip dans wifizone table si nécessaire
        // Pour l'instant on mock ou on prend une valeur par défaut
        $mikrotik_ip = '10.5.50.1'; // IP par défaut Hotspot Mikrotik

        return view('PortailClient.ticket', compact('ticket', 'wifizone', 'mikrotik_ip'));
    }
    
    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        // Récupérer le token de la wifizone depuis la session avant de détruire la session
        $wifizoneToken = session('wifizone_token');
        
        // Si pas de token en session, essayer de récupérer depuis la zone_id
        if (!$wifizoneToken && session('zone_id')) {
            $zone = \App\Models\WifiZone::find(session('zone_id'));
            if ($zone) {
                $wifizoneToken = $zone->token;
            }
        }
        
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Rediriger vers la landing avec le token si disponible
        if ($wifizoneToken) {
            return redirect()->route('client.landing', ['token' => $wifizoneToken]);
        }
        
        // Fallback si pas de token
        return redirect('/');
    }

    /**
     * Affiche la page de confirmation après réinitialisation du mot de passe
     */
    public function showPasswordResetSuccess(Request $request, $token)
    {
        return view('PortailClient.password-reset-success', [
            'redirectUrl' => url('/portal/landing/' . $token),
            'token' => $token
        ]);
    }
}
