<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller pour les analytics globaux
 */
class GlobalAnalyticsController extends Controller
{
    /**
     * Affiche les analytics globaux
     */
    public function index(Request $request)
    {
        // Période de temps
        $period = $request->get('period', '7d');

        // Données statiques pour la démo
        $stats = [
            'total_proprios' => 45,
            'total_wifizones' => 128,
            'total_transactions' => 1523,
            'total_revenue' => 15750000,
            'pending_withdrawals' => 5,
            'pending_withdrawals_amount' => 125000,
        ];

        // Revenus par jour (pour le graphique)
        $revenueByDay = collect([
            ['date' => now()->subDays(6)->format('Y-m-d'), 'amount' => 2500000],
            ['date' => now()->subDays(5)->format('Y-m-d'), 'amount' => 2200000],
            ['date' => now()->subDays(4)->format('Y-m-d'), 'amount' => 2800000],
            ['date' => now()->subDays(3)->format('Y-m-d'), 'amount' => 1950000],
            ['date' => now()->subDays(2)->format('Y-m-d'), 'amount' => 3100000],
            ['date' => now()->subDays(1)->format('Y-m-d'), 'amount' => 2350000],
            ['date' => now()->format('Y-m-d'), 'amount' => 850000],
        ]);

        // Top proprietaires par revenus
        $topProprietaires = collect([
            ['nom' => 'Koffi Amani', 'revenue' => 3500000, 'zones' => 5],
            ['nom' => 'Diallo Mamadou', 'revenue' => 2800000, 'zones' => 3],
            ['nom' => 'N\'guessan Konan', 'revenue' => 2100000, 'zones' => 4],
            ['nom' => 'Sow Fatou', 'revenue' => 1800000, 'zones' => 2],
            ['nom' => 'Acket Jean', 'revenue' => 1500000, 'zones' => 6],
        ]);

        // Zones par statut
        $zonesByStatus = [
            'online' => 95,
            'offline' => 15,
            'maintenance' => 18,
        ];

        // Transactions par type
        $transactionsByType = [
            'deposit' => 1200,
            'withdrawal' => 323,
        ];

        return view('superadmin.super_admin_analytics', compact(
            'stats',
            'revenueByDay',
            'topProprietaires',
            'zonesByStatus',
            'transactionsByType',
            'period'
        ));
    }

    /**
     * Export les données analytics
     */
    public function export(Request $request)
    {
        // Logique d'export à implémenter
        return back()->with('success', 'Export généré avec succès');
    }

    /**
     * API: données pour les graphiques
     */
    public function chartData(Request $request)
    {
        $period = $request->get('period', '7d');

        $data = [
            'revenue' => [
                ['date' => now()->subDays(6)->format('Y-m-d'), 'amount' => 2500000],
                ['date' => now()->subDays(5)->format('Y-m-d'), 'amount' => 2200000],
                ['date' => now()->subDays(4)->format('Y-m-d'), 'amount' => 2800000],
                ['date' => now()->subDays(3)->format('Y-m-d'), 'amount' => 1950000],
                ['date' => now()->subDays(2)->format('Y-m-d'), 'amount' => 3100000],
                ['date' => now()->subDays(1)->format('Y-m-d'), 'amount' => 2350000],
                ['date' => now()->format('Y-m-d'), 'amount' => 850000],
            ],
            'transactions' => [
                ['date' => now()->subDays(6)->format('Y-m-d'), 'count' => 45],
                ['date' => now()->subDays(5)->format('Y-m-d'), 'count' => 38],
                ['date' => now()->subDays(4)->format('Y-m-d'), 'count' => 52],
                ['date' => now()->subDays(3)->format('Y-m-d'), 'count' => 41],
                ['date' => now()->subDays(2)->format('Y-m-d'), 'count' => 67],
                ['date' => now()->subDays(1)->format('Y-m-d'), 'count' => 49],
                ['date' => now()->format('Y-m-d'), 'count' => 23],
            ],
        ];

        return response()->json($data);
    }
}
