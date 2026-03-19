<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\BlockedClient;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des clients.
 * 
 * Ce contrôleur gère toutes les opérations liées aux clients,
 * y compris l'affichage, la création, la modification, la suppression et le blocage/déblocage.
 */
class ClientController extends Controller
{
    /**
     * Affiche la page de gestion des clients avec filtres et pagination.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterType = $request->input('filter_type');
        $perPage = $request->input('per_page', 10);

        // Récupérer le propriétaire connecté
        $proprio = auth('proprio')->user();

        // 1. Récupérer les clients (Recherche + Filtres + Pagination)
        // On filtre pour n'afficher que les clients qui ont un ticket acheté dans une zone WiFi du proprio connecté
        $clientsQuery = Client::query()
            // Filtrer par proprio connecté (via tickets -> forfaits -> wifizones -> proprio)
            ->when($proprio, function($q) use ($proprio) {
                $q->whereHas('tickets', function($ticketQuery) use ($proprio) {
                    $ticketQuery->whereHas('forfait', function($forfaitQuery) use ($proprio) {
                        $forfaitQuery->whereHas('wifizone', function($zoneQuery) use ($proprio) {
                            $zoneQuery->where('proprio_id', $proprio->id);
                        });
                    });
                });
            })
            // Filtre de recherche (nom, téléphone, MAC address)
            ->when($search, function($q) use ($search) {
                $q->where('nom_complet', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('mac_address', 'like', "%{$search}%");
            })
            // Filtre nouveaux clients (moins de 30 jours)
            ->when($filterType === 'new', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })
            // Filtre clients bloqués
            ->when($filterType === 'blocked', function($q) {
                $q->where('is_blocked', true);
            });
        
        // Récupérer les IDs des wifizones du propriétaire pour le calcul du total
        $proprioZoneIds = $proprio ? $proprio->wifizones()->pluck('id')->toArray() : null;
        
        // Pour VIP, on filtre après avoir calculé le total
        if ($filterType === 'vip') {
            // Pour le filtre VIP, on applique d'abord le whereHas puis on calcule
            $clients = $clientsQuery->with(['tickets.forfait.wifizone'])
                ->get()
                ->map(function($client) use ($proprio, $proprioZoneIds) {
                    $total = 0;
                    $derniereZone = null;
                    $lastTicketDate = null;
                    
                    foreach ($client->tickets as $ticket) {
                        // Ne compter que les tickets vendus (statut = 'vendu')
                        // Utiliser uniquement prix_achat pour éviter les erreurs si forfait supprimé
                        if ($ticket->statut === 'vendu') {
                            $zoneId = $ticket->forfait->wifizones_id ?? null;
                            
                            // Vérifier si la zone appartient au proprio
                            if ($proprioZoneIds && in_array($zoneId, $proprioZoneIds)) {
                                $total += $ticket->prix_achat ?? 0;
                                
                                // Calculer la dernière zone visitée (basée sur date_vente)
                                if ($ticket->date_vente && (!$lastTicketDate || $ticket->date_vente > $lastTicketDate)) {
                                    $lastTicketDate = $ticket->date_vente;
                                    $derniereZone = $ticket->forfait->wifizone->nom_zone ?? null;
                                }
                            }
                        }
                    }
                    
                    $client->total_depense_calculated = $total;
                    $client->derniere_zone = $derniereZone;
                    $client->is_blocked = $proprio ? $client->isBlockedBy($proprio->id) : false;
                    return $client;
                })
                ->filter(fn($c) => $c->total_depense_calculated > 10000);
            
            // Paginer manuellement
            $total = $clients->count();
            $page = request()->get('page', 1);
            $clients = new \Illuminate\Pagination\LengthAwarePaginator(
                $clients->forPage($page, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => request()->url()]
            );
        } else {
            $clients = $clientsQuery
                ->with(['tickets.forfait.wifizone'])
                ->get()
                ->map(function($client) use ($proprio, $proprioZoneIds) {
                    $total = 0;
                    $derniereZone = null;
                    $lastTicketDate = null;
                    
                    foreach ($client->tickets as $ticket) {
                        // Ne compter que les tickets vendus (statut = 'vendu')
                        // Utiliser uniquement prix_achat pour éviter les erreurs si forfait supprimé
                        if ($ticket->statut === 'vendu') {
                            $zoneId = $ticket->forfait->wifizones_id ?? null;
                            
                            // Vérifier si la zone appartient au proprio
                            if ($proprioZoneIds && in_array($zoneId, $proprioZoneIds)) {
                                $total += $ticket->prix_achat ?? 0;
                                
                                // Calculer la dernière zone visitée (basée sur date_vente)
                                if ($ticket->date_vente && (!$lastTicketDate || $ticket->date_vente > $lastTicketDate)) {
                                    $lastTicketDate = $ticket->date_vente;
                                    $derniereZone = $ticket->forfait->wifizone->nom_zone ?? null;
                                }
                            }
                        }
                    }
                    
                    $client->total_depense_calculated = $total;
                    $client->derniere_zone = $derniereZone;
                    $client->is_blocked = $proprio ? $client->isBlockedBy($proprio->id) : false;
                    return $client;
                })
                ->sortByDesc('created_at');
            
            // Paginer manuellement
            $total = $clients->count();
            $page = request()->get('page', 1);
            $clients = new \Illuminate\Pagination\LengthAwarePaginator(
                $clients->forPage($page, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => request()->url()]
            );
        }

        // 2. Calculer les KPIs pour le dashboard clients (uniquement pour le proprio connecté)
        $totalClients = Client::query()
            ->when($proprio, function($q) use ($proprio) {
                $q->whereHas('tickets', function($ticketQuery) use ($proprio) {
                    $ticketQuery->whereHas('forfait', function($forfaitQuery) use ($proprio) {
                        $forfaitQuery->whereHas('wifizone', function($zoneQuery) use ($proprio) {
                            $zoneQuery->where('proprio_id', $proprio->id);
                        });
                    });
                });
            })
            ->count();
        
        $nouveauxClients = Client::query()
            ->when($proprio, function($q) use ($proprio) {
                $q->whereHas('tickets', function($ticketQuery) use ($proprio) {
                    $ticketQuery->whereHas('forfait', function($forfaitQuery) use ($proprio) {
                        $forfaitQuery->whereHas('wifizone', function($zoneQuery) use ($proprio) {
                            $zoneQuery->where('proprio_id', $proprio->id);
                        });
                    });
                });
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $clientsVIP = Client::query()
            ->when($proprio, function($q) use ($proprio) {
                $q->whereHas('tickets', function($ticketQuery) use ($proprio) {
                    $ticketQuery->whereHas('forfait', function($forfaitQuery) use ($proprio) {
                        $forfaitQuery->whereHas('wifizone', function($zoneQuery) use ($proprio) {
                            $zoneQuery->where('proprio_id', $proprio->id);
                        });
                    });
                });
            })
            ->with('tickets.forfait.wifizone')
            ->get()
            ->filter(function($client) use ($proprio) {
                $total = 0;
                foreach ($client->tickets as $ticket) {
                    if ($ticket->forfait && $ticket->forfait->wifizone) {
                        if (!$proprio || $ticket->forfait->wifizone->proprio_id == $proprio->id) {
                            $total += $ticket->prix_achat ?? 0;
                        }
                    }
                }
                return $total > 10000;
            })
            ->count();

        // 3. Envoyer les données à la vue
        return view('clients', compact('clients', 'totalClients', 'nouveauxClients', 'clientsVIP'));
    }

    /**
     * Traite la création d'un nouveau client.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Préparer le numéro complet (code pays + numéro local)
        $phoneCode = $request->input('phone_code', '+229');
        $localPhone = $request->input('telephone');
        $fullPhone = $phoneCode . ' ' . $localPhone;

        // Ajouter au request pour la validation unique
        $request->merge(['telephone' => $fullPhone]);

        // 2. Validation des données
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'required|string|unique:clients,telephone|max:20',
            'total_depense' => 'nullable|integer'
        ]);

        // 3. Création du client dans la base de données
        Client::create([
            'nom_complet' => $request->nom_complet,
            'telephone' => $fullPhone,
            'total_depense' => $request->total_depense ?? 0,
            'derniere_zone' => 'Manuel', // Indique que le client a été ajouté manuellement
            'is_blocked' => false // Client non bloqué par défaut
        ]);

        // 4. Redirection avec message de succès
        return redirect()->route('clients')->with('success', 'Client ajouté avec succès !');
    }

    /**
     * Traite la mise à jour d'un client existant.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        // Validation des données
        $request->validate([
             'nom_complet' => 'required|string|max:255',
             'telephone' => 'required|string|max:20|unique:clients,telephone,'.$id,
        ]);

        // Mise à jour du client
        $client->update([
            'nom_complet' => $request->nom_complet,
            'telephone' => $request->telephone
        ]);

        return response()->json(['success' => true, 'message' => 'Client mis à jour']);
    }

    /**
     * Bascule le statut de blocage d'un client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBlock(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $proprio = auth('proprio')->user();
        
        // Vérifier si le client est déjà bloqué par ce propriétaire
        $blocked = BlockedClient::where('client_id', $client->id)
            ->where('proprio_id', $proprio->id)
            ->first();
        
        if ($blocked) {
            // Débloquer le client
            $blocked->delete();
            return response()->json([
                'success' => true, 
                'message' => 'Client débloqué avec succès',
                'is_blocked' => false
            ]);
        } else {
            // Bloquer le client définitivement (blocked_until = null = permanent)
            BlockedClient::create([
                'proprio_id' => $proprio->id,
                'client_id' => $client->id,
                'blocked_until' => null, // Blocage permanent jusqu'à déblocage manuel
                'reason' => $request->input('reason', 'Blocage par le propriétaire')
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Client bloqué définitivement',
                'is_blocked' => true
            ]);
        }
    }

    /**
     * Vérifie si un client est bloqué par le propriétaire actuel
     */
    public function isBlockedByProprio($clientId)
    {
        $proprio = auth('proprio')->user();
        return BlockedClient::isBlocked($clientId, $proprio->id);
    }

    /**
     * Récupère l'historique des tickets d'un client.
     * Filtré par les wifizones du propriétaire connecté
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function history($id)
    {
        $client = Client::findOrFail($id);
        
        // Récupérer le propriétaire connecté
        $proprio = auth('proprio')->user();
        
        // Récupérer les IDs des wifizones du propriétaire
        $zoneIds = $proprio->wifizones()->pluck('id');
        
        // Filtrer les tickets par les wifizones du propriétaire et par statut 'vendu'
        $tickets = $client->tickets()
            ->where('statut', 'vendu')
            ->whereHas('forfait', function($query) use ($zoneIds) {
                $query->whereIn('wifizones_id', $zoneIds);
            })
            ->with(['forfait.wifizone'])
            ->latest()
            ->take(50)
            ->get();
        
        // Transformation des données pour la réponse JSON
        // Calculer le total dépensé dans les zones du proprio
        // Utiliser uniquement prix_achat pour éviter les erreurs si forfait supprimé
        $totalSpent = $tickets->sum(function($t) {
            return $t->prix_achat ?? 0;
        });
        
        return response()->json([
            'success' => true, 
            'total_spent' => $totalSpent,
            'tickets' => $tickets->map(function($t) {
                return [
                    'date' => ($t->date_vente ?? $t->created_at)->format('d M Y H:i'),
                    'forfait' => $t->forfait->nom ?? 'Inconnu',
                    'zone' => $t->forfait->wifizone->nom_zone ?? '-',
                    'prix' => number_format($t->prix_achat ?? 0, 0, ',', ' ') . ' F',
                    'login' => $t->username,
                    'password' => $t->password
                ];
            })
        ]);
    }

    /**
     * Traite la suppression d'un client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['success' => true, 'message' => 'Client supprimé avec succès']);
    }

    /**
     * Réinitialise le mot de passe d'un client à 12345
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($id, Request $request)
    {
        $client = Client::findOrFail($id);
        
        // Hasher le nouveau mot de passe
        $newPassword = '12345';
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($newPassword);
        
        // Mettre à jour le mot de passe
        $client->update([
            'password' => $hashedPassword
        ]);
        
        // Logger l'action
        \Illuminate\Support\Facades\Log::info('Mot de passe réinitialisé', [
            'client_id' => $client->id,
            'client_telephone' => $client->telephone,
            'reset_by' => auth('proprio')->user()->id,
            'timestamp' => now()->toISOString()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Le mot de passe a été réinitialisé à 12345 avec succès'
        ]);
    }

    /**
     * Génère un lien de réinitialisation de mot de passe pour un client
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateResetPasswordLink($id)
    {
        $client = Client::findOrFail($id);
        
        // Générer un token unique
        $token = \Illuminate\Support\Str::random(64);
        
        // Stocker le token dans la base de données
        \Illuminate\Support\Facades\DB::table('client_password_resets')->updateOrInsert(
            ['telephone' => $client->telephone],
            [
                'telephone' => $client->telephone,
                'token' => hash('sha256', $token),
                'created_at' => now()
            ]
        );
        
        // Générer l'URL de réinitialisation
        $resetUrl = url('/portal/reset-password/' . $token . '?telephone=' . urlencode($client->telephone));
        
        return response()->json([
            'success' => true,
            'message' => 'Lien de réinitialisation généré avec succès',
            'reset_url' => $resetUrl
        ]);
    }
}
