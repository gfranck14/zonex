<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des propriétaires
 */
class ProprioManagementController extends Controller
{
    /**
     * Liste tous les propriétaires
     */
    public function index(Request $request)
    {
        // Données statiques pour la démo
        $proprios = collect([
            (object)['id' => 1, 'nom' => 'Koffi', 'prenom' => 'Amani', 'email' => 'koffi@email.com', 'numero' => '2250102030405', 'is_active' => true, 'created_at' => now()->subDays(30), 'wifizones_count' => 5],
            (object)['id' => 2, 'nom' => 'Diallo', 'prenom' => 'Mamadou', 'email' => 'diallo@email.com', 'numero' => '2250102030406', 'is_active' => true, 'created_at' => now()->subDays(25), 'wifizones_count' => 3],
            (object)['id' => 3, 'nom' => 'N\'guessan', 'prenom' => 'Konan', 'email' => 'nguessan@email.com', 'numero' => '2250102030407', 'is_active' => true, 'created_at' => now()->subDays(20), 'wifizones_count' => 4],
            (object)['id' => 4, 'nom' => 'Sow', 'prenom' => 'Fatou', 'email' => 'sow@email.com', 'numero' => '2250102030408', 'is_active' => false, 'created_at' => now()->subDays(15), 'wifizones_count' => 2],
            (object)['id' => 5, 'nom' => 'Acket', 'prenom' => 'Jean', 'email' => 'acket@email.com', 'numero' => '2250102030409', 'is_active' => true, 'created_at' => now()->subDays(10), 'wifizones_count' => 6],
        ]);

        // Paginer manuellement
        $perPage = 20;
        $page = $request->get('page', 1);
        $total = $proprios->count();
        $items = $proprios->forPage($page, $perPage);
        
        $proprios = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => route('superadmin.proprios.index')]
        );

        return view('superadmin.super_admin_proprios', compact('proprios'));
    }

    /**
     * Affiche les détails d'un propriétaire
     */
    public function show($id)
    {
        // Données statiques pour la démo
        $proprio = (object)[
            'id' => $id,
            'nom' => 'Koffi',
            'prenom' => 'Amani',
            'email' => 'koffi@email.com',
            'numero' => '2250102030405',
            'is_active' => true,
            'created_at' => now()->subDays(30),
            'wifizones' => collect([
                (object)['id' => 1, 'nom' => 'Café Internet Centre', 'is_online' => true],
                (object)['id' => 2, 'nom' => 'Cyber Bac', 'is_online' => true],
            ]),
            'transactions' => collect([
                (object)['id' => 1, 'type' => 'deposit', 'amount' => 5000, 'created_at' => now()->subDays(1)],
                (object)['id' => 2, 'type' => 'deposit', 'amount' => 2500, 'created_at' => now()->subDays(2)],
            ])
        ];
        
        // Stats
        $stats = [
            'total_zones' => 5,
            'total_transactions' => 45,
            'total_deposits' => 125000,
            'total_withdrawals' => 50000,
        ];

        // Transactions récentes
        $recentTransactions = collect([
            (object)['id' => 1, 'type' => 'deposit', 'amount' => 5000, 'created_at' => now()->subDays(1)],
            (object)['id' => 2, 'type' => 'deposit', 'amount' => 2500, 'created_at' => now()->subDays(2)],
        ]);

        return view('superadmin.super_admin_proprios', compact('proprio', 'stats', 'recentTransactions'));
    }

    /**
     * Active/désactive un propriétaire
     */
    public function toggleActive(Request $request, $id)
    {
        return back()->with('success', 'Statut mis à jour avec succès');
    }

    /**
     * Impersonate un propriétaire
     */
    public function impersonate(Request $request, $id)
    {
        return redirect()->route('dashboard')
            ->with('info', 'Vous êtes maintenant connecté en tant que proprietaire');
    }

    /**
     * Arrête l'impersonation
     */
    public function stopImpersonation(Request $request)
    {
        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Impersonation terminée');
    }
}
