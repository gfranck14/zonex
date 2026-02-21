<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Retrait;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    /**
     * Affiche le dashboard super admin
     */
    public function index()
    {
        // Stats statiques (données de démonstration)
        $stats = [
            'total_proprios' => 156,
            'active_proprios' => 148,
            'total_clients' => 1245,
            'total_zones' => 423,
            'total_transactions' => 5678,
            'total_revenue' => 12500000,
            'pending_withdrawals' => 12,
            'pending_withdrawals_amount' => 850000,
            'open_tickets' => 8,
        ];

        // Revenus du mois (statique)
        $monthlyRevenue = 2500000;

        // Revenus du mois dernier (statique)
        $lastMonthRevenue = 2200000;

        // Nouveaux propriétaires ce mois (statique)
        $newPropriosThisMonth = 15;

        // Top 5 propriétaires (statique)
        $topProprios = collect([
            (object)['nom' => 'Koffi', 'prenom' => 'Amani', 'total_deposits' => 1250000],
            (object)['nom' => 'Diallo', 'prenom' => 'Mamadou', 'total_deposits' => 980000],
            (object)['nom' => 'N\'guessan', 'prenom' => 'Konan', 'total_deposits' => 875000],
            (object)['nom' => 'Sow', 'prenom' => 'Fatou', 'total_deposits' => 720000],
            (object)['nom' => 'Acket', 'prenom' => 'Jean', 'total_deposits' => 650000],
        ]);

        // Activité récente (statique)
        $recentActivity = collect([
            (object)['action' => 'login', 'created_at' => now()->subMinutes(5)],
            (object)['action' => 'approve_retrait', 'created_at' => now()->subMinutes(15)],
            (object)['action' => 'create_proprio', 'created_at' => now()->subMinutes(30)],
            (object)['action' => 'update_settings', 'created_at' => now()->subHours(1)],
            (object)['action' => 'reject_retrait', 'created_at' => now()->subHours(2)],
        ]);

        // Transactions récentes (statique)
        $recentTransactions = collect([
            (object)['type' => 'deposit', 'amount' => 5000, 'created_at' => now()->subMinutes(2), 'proprio' => (object)['nom' => 'Dupont']],
            (object)['type' => 'deposit', 'amount' => 2500, 'created_at' => now()->subMinutes(15), 'proprio' => (object)['nom' => 'Marie']],
            (object)['type' => 'withdrawal', 'amount' => 45000, 'created_at' => now()->subHours(1), 'proprio' => (object)['nom' => 'Café Internet']],
            (object)['type' => 'deposit', 'amount' => 10000, 'created_at' => now()->subHours(2), 'proprio' => (object)['nom' => 'Koffi']],
            (object)['type' => 'deposit', 'amount' => 7500, 'created_at' => now()->subHours(3), 'proprio' => (object)['nom' => 'Ben']],
        ]);

        // ==============================================
        // DONNÉES DU JOUR (Temps réel)
        // ==============================================
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Paiements réussis du jour
        $paiementsReussisJour = Paiement::where('statut', 'reussi')
            ->whereBetween('created_at', [$today, $todayEnd])
            ->get();
        $paiementsReussisCount = $paiementsReussisJour->count();
        $paiementsReussisMontant = $paiementsReussisJour->sum('montant');

        // Paiements échoués du jour
        $paiementsEchouesJour = Paiement::whereIn('statut', ['echoue', 'annule'])
            ->whereBetween('created_at', [$today, $todayEnd])
            ->get();
        $paiementsEchouesCount = $paiementsEchouesJour->count();
        $paiementsEchouesMontant = $paiementsEchouesJour->sum('montant');

        // Retraits en attente
        $retraitsEnAttente = Retrait::where('status', 'pending')
            ->get();
        $retraitsEnAttenteCount = $retraitsEnAttente->count();
        $retraitsEnAttenteMontant = $retraitsEnAttente->sum('amount');

        // Retraits payés (complétés)
        $retraitsPayes = Retrait::whereIn('status', ['completed', 'processing'])
            ->whereBetween('created_at', [$today, $todayEnd])
            ->get();
        $retraitsPayesCount = $retraitsPayes->count();
        $retraitsPayesMontant = $retraitsPayes->sum('amount');

        // Commission du jour (ccorp_fee des retraits complétés)
        $commissionJour = Retrait::whereIn('status', ['completed', 'processing'])
            ->whereBetween('created_at', [$today, $todayEnd])
            ->sum('ccorp_fee');

        return view('superadmin.super_admin_dashboard', compact(
            'stats',
            'monthlyRevenue',
            'lastMonthRevenue',
            'newPropriosThisMonth',
            'topProprios',
            'recentActivity',
            'recentTransactions',
            'paiementsReussisCount',
            'paiementsReussisMontant',
            'paiementsEchouesCount',
            'paiementsEchouesMontant',
            'retraitsEnAttenteCount',
            'retraitsEnAttenteMontant',
            'retraitsPayesCount',
            'retraitsPayesMontant',
            'commissionJour'
        ));
    }

    /**
     * API: Stats en temps réel (statique)
     */
    public function getRealtimeStats()
    {
        return response()->json([
            'total_proprios' => 156,
            'active_proprios' => 148,
            'total_transactions' => 5678,
            'pending_withdrawals' => 12,
            'open_tickets' => 8,
            'total_revenue' => 12500000,
            'last_updated' => now()->toIso8601String(),
        ]);
    }

    /**
     * API: Données pour le graphique de revenus (statique)
     */
    public function getRevenueChart()
    {
        $months = [];
        $revenues = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $revenues[] = rand(1500000, 3500000);
        }

        return response()->json([
            'labels' => $months,
            'datasets' => [[
                'label' => 'Revenus',
                'data' => $revenues,
                'borderColor' => '#dc2626',
                'backgroundColor' => 'rgba(220, 38, 38, 0.1)',
                'fill' => true,
            ]]
        ]);
    }
}
