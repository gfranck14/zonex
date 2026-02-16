<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Retrait;
use App\Models\Proprio;
use App\Models\WifiZone;
use App\Models\Forfait;
use App\Models\Ticket;

class SuperAdminController extends Controller
{
    /**
     * Affiche la liste des propriétaires
     */
    public function proprietaires(Request $request)
    {
        // Récupérer les propriétaires avec leurs zones, forfaits et tickets
        $proprios = Proprio::with(['wifizones.forfaits' => function($query) {
            $query->withCount(['tickets as tickets_count' => function($q) {
                $q->where('statut', 'libre');
            }]);
            $query->withCount(['tickets as sales_today_count' => function($q) {
                $q->where('statut', 'vendu')
                  ->whereDate('date_vente', now());
            }]);
        }])->get();
        
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
        return view('superadmin.clients');
    }

    /**
     * Affiche la liste des retraits
     */
    public function retraits(Request $request)
    {
        // Récupérer les retraits avec la relation proprio (pour avoir nom et prenom)
        $retraits = Retrait::with('proprio')->get();
        
        // Si pas de données en base, on utilise des données statiques pour la démo
        if ($retraits->isEmpty()) {
            $retraits = collect([
                (object)[
                    'id' => 1,
                    'reference' => 'RET-001',
                    'user_id' => 1,
                    'amount' => 25000,
                    'phone_number' => '2250102030405',
                    'status' => 'pending',
                    'requested_at' => now()->subDays(2)->setTime(14, 30),
                    'proprio' => (object)['nom' => 'Koffi', 'prenom' => 'Amani'],
                ],
                (object)[
                    'id' => 2,
                    'reference' => 'RET-002',
                    'user_id' => 2,
                    'amount' => 15000,
                    'phone_number' => '2250102030406',
                    'status' => 'pending',
                    'requested_at' => now()->subDays(3)->setTime(10, 15),
                    'proprio' => (object)['nom' => 'Diallo', 'prenom' => 'Mamadou'],
                ],
                (object)[
                    'id' => 3,
                    'reference' => 'RET-003',
                    'user_id' => 3,
                    'amount' => 50000,
                    'phone_number' => '2250102030407',
                    'status' => 'completed',
                    'requested_at' => now()->subDays(4)->setTime(16, 45),
                    'proprio' => (object)['nom' => "N'guessan", 'prenom' => 'Konan'],
                ],
                (object)[
                    'id' => 4,
                    'reference' => 'RET-004',
                    'user_id' => 4,
                    'amount' => 10000,
                    'phone_number' => '2250102030408',
                    'status' => 'cancelled',
                    'requested_at' => now()->subDays(5)->setTime(9, 0),
                    'proprio' => (object)['nom' => 'Sow', 'prenom' => 'Fatou'],
                ],
                (object)[
                    'id' => 5,
                    'reference' => 'RET-005',
                    'user_id' => 5,
                    'amount' => 30000,
                    'phone_number' => '2250102030409',
                    'status' => 'completed',
                    'requested_at' => now()->subDays(6)->setTime(11, 20),
                    'proprio' => (object)['nom' => 'Acket', 'prenom' => 'Jean'],
                ],
                (object)[
                    'id' => 6,
                    'reference' => 'RET-006',
                    'user_id' => 6,
                    'amount' => 8500,
                    'phone_number' => '2250506070809',
                    'status' => 'cancelled',
                    'requested_at' => now()->subDays(7)->setTime(8, 30),
                    'proprio' => (object)['nom' => 'Traore', 'prenom' => 'Bakary'],
                ],
            ]);
        }
        
        return view('superadmin.retraits', compact('retraits'));
    }
}
