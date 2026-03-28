<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WifiZone;
use App\Models\Ticket;   
use Illuminate\Support\Str;  
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RouterOS\Config;
use RouterOS\Client;
use RouterOS\Query;

class WifizoneController extends Controller
{
    /**
     * Affiche la page Wifi Zones
     */
    public function index()
    {
         // 1. Récupérer l'ID du proprio connecté
        $proprioId = Auth::guard('proprio')->id();

        // 2. Récupérer ses zones WiFi avec les forfaits et le décompte des tickets
        $zones = WifiZone::where('proprio_id', $proprioId)
            ->with(['forfaits' => function($query) {
                $query->withCount(['tickets as tickets_count' => function($q) {
                    $q->where('statut', 'libre');
                }]);
                $query->withCount(['tickets as sales_today_count' => function($q) {
                    $q->where('statut', 'vendu')
                      ->whereDate('date_vente', now());
                }]);
            }])
            ->get();

        // 3. Envoyer les zones à la vue
        return view('proprio.wifizones', compact('zones'));
    }


     public function store(Request $request) 
    {
        try {
            // Validation des données entrantes
            $data = $request->validate([
                'nom_zone' => 'required|string|max:255',
                'adresse' => 'nullable|string|max:255',
                'hotspot_address' => 'nullable|string|max:255',
            ]);

            // Récupération de l'ID du proprio connecté
            $proprioId = Auth::guard('proprio')->id();
            
            if (!$proprioId) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
            }

            // Création de la zone
            $zone = WifiZone::create([
                'proprio_id' => $proprioId,
                'nom_zone' => $data['nom_zone'],
                'adresse' => $data['adresse'],
                'hotspot_address' => $data['hotspot_address'] ?? null,
                'api_host' => $request->api_host,
                'api_port' => $request->api_port ?: 8728,
                'api_user' => $request->api_user,
                'api_password' => $request->api_password ? Crypt::encryptString($request->api_password) : null,
                'token' => 'wz_' . Str::random(10),
            ]);

            return response()->json([
                'success' => true,
                'token' => $zone->token,
                'url' => url('/portal/landing/' . $zone->token )
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une zone WiFi
     */
    public function update(Request $request, $id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->firstOrFail();

            $data = $request->validate([
                'nom_zone' => 'sometimes|required|string|max:255',
                'adresse' => 'nullable|string|max:255',
                'hotspot_address' => 'nullable|string|max:255',
                'display_name' => 'nullable|string|max:255',
                'welcome_message' => 'nullable|string',
                'primary_color' => 'nullable|string|max:50',
            ]);

            // Prepare update data
            $updateData = [];

            if (isset($data['nom_zone'])) $updateData['nom_zone'] = $data['nom_zone'];

            // Only update fields that were provided
            if (isset($data['adresse'])) $updateData['adresse'] = $data['adresse'];
            if (isset($data['hotspot_address'])) $updateData['hotspot_address'] = $data['hotspot_address'];
            if (isset($data['display_name'])) $updateData['display_name'] = $data['display_name'];
            if (isset($data['welcome_message'])) $updateData['welcome_message'] = $data['welcome_message'];
            if (isset($data['primary_color'])) $updateData['primary_color'] = $data['primary_color'];

            // Handle API credentials if provided
            if ($request->has('api_host')) {
                $updateData['api_host'] = $request->api_host;
                $updateData['api_port'] = $request->api_port ?: 8728;
                $updateData['api_user'] = $request->api_user;
                if ($request->api_password) {
                    $updateData['api_password'] = Crypt::encryptString($request->api_password);
                }
            } 
            
            $zone->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Zone mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour zone {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une zone WiFi (Soft Delete)
     * 
     * Si `clean_mikrotik` est envoyé, on nettoie aussi le routeur :
     * Déconnexion clients → Suppression tickets → Suppression profils
     */
    public function destroy(Request $request, $id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->with('forfaits')
                ->firstOrFail();

            // --- NETTOYAGE MIKROTIK (optionnel) ---
            if ($request->has('clean_mikrotik') && $request->clean_mikrotik == '1') {
                try {
                    $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                    
                    foreach ($zone->forfaits as $forfait) {
                        // 1. Déconnecter les clients actifs
                        $activeSessions = $mikrotikService->getActiveSessionsByProfile($zone, $forfait->nom);
                        if (!empty($activeSessions)) {
                            $sessionIds = array_column($activeSessions, '.id');
                            $mikrotikService->removeActiveSessions($zone, $sessionIds);
                        }
                        
                        // 2. Supprimer les utilisateurs (tickets) du profil
                        $mikrotikService->removeUsersByProfile($zone, $forfait->nom);
                        
                        // 3. Supprimer le profil lui-même
                        $mikrotikService->removeProfileByName($zone, $forfait->nom);
                    }
                    
                    Log::info("🗑️ Nettoyage MikroTik complet pour la zone '{$zone->nom_zone}'");
                } catch (\Exception $e) {
                    Log::warning("⚠️ Échec nettoyage MikroTik pour la zone '{$zone->nom_zone}' : " . $e->getMessage());
                }
            }

            // --- SOFT DELETE LOCAL ---
            // Soft Delete de tous les tickets de tous les forfaits
            foreach ($zone->forfaits as $forfait) {
                $forfait->tickets()->delete();
            }
            // Soft Delete de tous les forfaits
            $zone->forfaits()->delete();
            // Soft Delete de la zone
            $zone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Zone supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère l'impact de la suppression d'une zone (nombre forfaits/tickets/paiements)
     */
    public function getImpact($id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->with(['forfaits' => function($q) {
                    $q->withCount('tickets');
                }])
                ->firstOrFail();

            $forfaitsCount = $zone->forfaits->count();
            $ticketsCount = $zone->forfaits->sum('tickets_count');
            
            // Compter les paiements associés aux forfaits de la zone
            $paiementsCount = \App\Models\Paiement::whereIn('forfait_id', $zone->forfaits->pluck('id'))->count();

            return response()->json([
                'success' => true,
                'forfaits_count' => $forfaitsCount,
                'tickets_count' => $ticketsCount,
                'paiements_count' => $paiementsCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Zone introuvable'], 404);
        }
    }

    /**
     * Récupère l'impact de la suppression des tickets d'une zone (avec détail sold vs free)
     */
    public function getTicketsImpact($id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->with(['forfaits' => function($q) {
                    $q->with(['tickets' => function($t) {
                        $t->select('id', 'statut', 'forfaits_id');
                    }]);
                }])
                ->firstOrFail();

            $totalTickets = 0;
            $freeTickets = 0;
            $soldTickets = 0;

            foreach ($zone->forfaits as $forfait) {
                foreach ($forfait->tickets as $ticket) {
                    $totalTickets++;
                    if ($ticket->statut === 'libre') {
                        $freeTickets++;
                    } else {
                        $soldTickets++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'total' => $totalTickets,
                'libre' => $freeTickets,
                'vendu' => $soldTickets
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Zone introuvable'], 404);
        }
    }

    /**
     * Supprime tous les tickets d'une zone WiFi
     */
    public function deleteZoneTickets($id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->with('forfaits')
                ->firstOrFail();

            // Récupérer les IDs des forfaits associés à la zone
            $forfaitIds = $zone->forfaits->pluck('id')->toArray();

            // Supprimer tous les tickets des forfaits de la zone
            $deletedCount = Ticket::whereIn('forfaits_id', $forfaitIds)->delete();

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' ticket(s) supprimé(s) avec succès',
                'count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarde les identifiants Ticket Admin
     */
    public function saveTicketCredentials(Request $request, $id)
    {
        try {
            $zone = WifiZone::where('proprio_id', Auth::guard('proprio')->id())
                ->where('id', $id)
                ->firstOrFail();

            $validated = $request->validate([
                'ticket_login' => 'required|string|max:255',
                'ticket_password' => 'required|string|max:255',
            ]);

            // Vérifier si c'est une création ou une modification
            $isCreation = !$zone->ticket_admin_username && !$zone->ticket_admin_password;
            
            $zone->update([
                'ticket_admin_username' => $validated['ticket_login'],
                'ticket_admin_password' => Crypt::encryptString($validated['ticket_password']),
            ]);

            $message = $isCreation 
                ? 'Identifiants Ticket Admin créés avec succès' 
                : 'Identifiants Ticket Admin modifiés avec succès';

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $isCreation ? 'created' : 'updated'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation : ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Teste la connexion au Mikrotik
     */
    public function testConnection(Request $request, \App\Services\MikrotikSyncService $mikrotikService)
    {
        try {
            $host = $request->api_host;
            $user = $request->api_user;
            $pass = $request->api_password;
            $port = $request->api_port ?: 8728;

            // Si le mot de passe n'est pas fourni, on essaie de récupérer celui en base
            if (empty($pass) && $request->zone_id) {
                $zone = WifiZone::where('id', $request->zone_id)
                    ->where('proprio_id', Auth::guard('proprio')->id())
                    ->first();
                if ($zone && $zone->api_password) {
                    try {
                        $pass = Crypt::decryptString($zone->api_password);
                        Log::info("Test connexion: utilisation du mot de passe stocké pour la zone ID: {$zone->id}");
                    } catch (\Exception $e) {
                        Log::error("Erreur décryptage mot de passe stocké: " . $e->getMessage());
                    }
                }
            }

            if (!$host || !$user || !$pass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Host, User et Password sont requis pour le test. Si vous avez déjà enregistré un mot de passe, assurez-vous de l\'host et l\'user également.'
                ], 400);
            }

            // Utilisation du Service Centralisé
            $result = $mikrotikService->testCustomConnection($host, $user, $pass, (int)$port);

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie !',
                'identity' => $result['identity'],
                'version' => $result['version']
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur test connexion zone {$request->zone_id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Échec de la connexion : ' . $e->getMessage()
            ]);
        }
    }
}