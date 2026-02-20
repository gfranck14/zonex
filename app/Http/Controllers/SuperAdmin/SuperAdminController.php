<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Retrait;
use App\Models\Proprio;
use App\Models\WifiZone;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\Client;

class SuperAdminController extends Controller
{
    /**
     * Affiche la liste des propriétaires
     */
    public function proprietaires(Request $request)
    {
        // Récupérer le nombre d'éléments par page (par défaut 10)
        $perPage = $request->input('per_page', 10);
        
        // Récupérer les propriétaires avec leurs zones, forfaits et tickets
        $proprios = Proprio::with(['wifizones.forfaits' => function($query) {
            $query->withCount(['tickets as tickets_count' => function($q) {
                $q->where('statut', 'libre');
            }]);
            $query->withCount(['tickets as sales_today_count' => function($q) {
                $q->where('statut', 'vendu')
                  ->whereDate('date_vente', now());
            }]);
        }])->paginate($perPage);
        
        // Si pas de données en base, on utilise des données statiques pour la démo
        if ($proprios->isEmpty()) {
            $proprios = collect([
                (object)[
                    'id' => 1,
                    'nom' => 'Koffi',
                    'prenom' => 'Amani',
                    'email' => 'koffi@email.com',
                    'numero' => '2250102030405',
                    'is_active' => true,
                    'created_at' => now()->subDays(30),
                    'wifizones' => collect([
                        (object)['id' => 1, 'nom_zone' => 'Café Internet Centre', 'adresse' => 'Cotonou, Haie Vive', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 128, 'sales_today_count' => 12]])],
                        (object)['id' => 2, 'nom_zone' => 'Cyber Bac', 'adresse' => 'Abomey-Calavi', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 45, 'sales_today_count' => 8]])],
                    ]),
                ],
                (object)[
                    'id' => 2,
                    'nom' => 'Diallo',
                    'prenom' => 'Mamadou',
                    'email' => 'diallo@email.com',
                    'numero' => '2250102030406',
                    'is_active' => true,
                    'created_at' => now()->subDays(25),
                    'wifizones' => collect([
                        (object)['id' => 3, 'nom_zone' => 'Restaurant Le Wifi', 'adresse' => 'Porto-Novo', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 200, 'sales_today_count' => 25]])],
                    ]),
                ],
                (object)[
                    'id' => 3,
                    'nom' => "N'guessan",
                    'prenom' => 'Konan',
                    'email' => 'nguessan@email.com',
                    'numero' => '2250102030407',
                    'is_active' => true,
                    'created_at' => now()->subDays(20),
                    'wifizones' => collect([
                        (object)['id' => 4, 'nom_zone' => 'Espace Numérique', 'adresse' => 'Cotonou, Calavi', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 15, 'sales_today_count' => 3]])],
                        (object)['id' => 5, 'nom_zone' => 'Hotel Connect', 'adresse' => 'Ouidah', 'is_online' => false, 'forfaits' => collect([(object)['tickets_count' => 5, 'sales_today_count' => 1]])],
                    ]),
                ],
                (object)[
                    'id' => 4,
                    'nom' => 'Sow',
                    'prenom' => 'Fatou',
                    'email' => 'sow@email.com',
                    'numero' => '2250102030408',
                    'is_active' => false,
                    'created_at' => now()->subDays(15),
                    'wifizones' => collect([]),
                ],
                (object)[
                    'id' => 5,
                    'nom' => 'Acket',
                    'prenom' => 'Jean',
                    'email' => 'acket@email.com',
                    'numero' => '2250102030409',
                    'is_active' => true,
                    'created_at' => now()->subDays(10),
                    'wifizones' => collect([
                        (object)['id' => 6, 'nom_zone' => 'Coworking Space', 'adresse' => 'Cotonou, Centre', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 150, 'sales_today_count' => 18]])],
                        (object)['id' => 7, 'nom_zone' => 'Library Wifi', 'adresse' => 'Parakou', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 80, 'sales_today_count' => 10]])],
                        (object)['id' => 8, 'nom_zone' => 'Airport Lounge', 'adresse' => 'Cotonou, AIBD', 'is_online' => true, 'forfaits' => collect([(object)['tickets_count' => 300, 'sales_today_count' => 45]])],
                    ]),
                ],
            ]);
        }
        
        return view('superadmin.proprietaires', compact('proprios'));
    }

    /**
     * Affiche la liste des clients
     */
    public function clients(Request $request)
    {
        // Récupérer le nombre d'éléments par page (par défaut 10)
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $filterType = $request->input('filter_type');
        
        // Récupérer les clients avec pagination
        $clientsQuery = Client::query()
            ->with(['tickets.forfait'])
            ->when($search, function($q) use ($search) {
                $q->where('nom_complet', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        
        // Appliquer le tri et calculer le total dépensé dynamiquement
        $clients = $clientsQuery->get()
            ->map(function($client) {
                $total = 0;
                foreach ($client->tickets as $ticket) {
                    // Ne compter que les tickets vendus (statut = 'vendu')
                    // Utiliser uniquement prix_achat pour éviter les erreurs si forfait supprimé
                    if ($ticket->statut === 'vendu') {
                        $total += $ticket->prix_achat ?? 0;
                    }
                }
                $client->total_depense_calculated = $total;
                return $client;
            });
        
        // Appliquer les filtres
        if ($filterType === 'vip') {
            $clients = $clients->filter(fn($c) => $c->total_depense_calculated > 10000);
        } elseif ($filterType === 'new') {
            $clients = $clients->filter(fn($c) => $c->created_at >= now()->subDays(30));
        } elseif ($filterType === 'blocked') {
            $clients = $clients->filter(fn($c) => $c->is_blocked);
        }
        
        // Trier par date décroissante
        $clients = $clients->sortByDesc('created_at');
        
        // Paginer manuellement
        $total = $clients->count();
        $page = $request->input('page', 1);
        $clients = new \Illuminate\Pagination\LengthAwarePaginator(
            $clients->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => $request->url()]
        );
        
        // Statistiques
        $stats = [
            'total' => Client::count(),
            'nouveaux_30j' => Client::where('created_at', '>=', now()->subDays(30))->count(),
            'vip' => Client::where('total_depense', '>', 10000)->count(),
        ];
        
        return view('superadmin.clients', compact('clients', 'stats'));
    }

    /**
     * Retourne les tickets d'un client (pour affichage via AJAX)
     */
    public function clientTickets($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // Charger les tickets avec les relations Forfait et WifiZone
        $ticketsQuery = $client->tickets()
            ->with(['forfait.wifizone'])
            ->where('statut', 'vendu')
            ->orderBy('date_vente', 'desc')
            ->limit(50);
        
        $tickets = $ticketsQuery->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'date' => $ticket->date_vente ? $ticket->date_vente->format('d M Y H:i') : '-',
                    'forfait_nom' => $ticket->forfait ? $ticket->forfait->nom : 'Inconnu',
                    'zone_nom' => ($ticket->forfait && $ticket->forfait->wifizone) ? $ticket->forfait->wifizone->nom_zone : '-',
                    // Utiliser uniquement prix_achat pour éviter les erreurs si forfait supprimé
                    'prix' => $ticket->prix_achat ?? 0,
                    'username' => $ticket->username,
                    'password' => $ticket->password,
                ];
            });
        
        // Calculer le total dépensé
        $totalSpent = $tickets->sum('prix');
        
        return response()->json([
            'client' => $client->nom_complet,
            'total_spent' => $totalSpent,
            'tickets' => $tickets
        ]);
    }

    /**
     * Affiche la liste des retraits
     */
    public function retraits(Request $request)
    {
        // Récupérer le nombre d'éléments par page (par défaut 10)
        $perPage = $request->input('per_page', 10);
        
        // Récupérer les retraits avec la relation proprio (pour avoir nom et prenom)
        // Triés par date décroissante (plus récents en premier)
        $retraits = Retrait::with('proprio')->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('superadmin.retraits', compact('retraits'));
    }
}
