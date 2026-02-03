<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proprio;
use App\Models\Wifizone;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

/**
 * Controller pour le dashboard principal du superadmin
 */
class SuperAdminDashboardController extends Controller
{
    /**
     * Affiche le dashboard principal
     */
    public function index()
    {
        // KPIs principaux
        $stats = [
            'total_proprios' => Proprio::count(),
            'proprios_actifs' => Proprio::where('is_active', true)->count(),
            'total_zones' => Wifizone::count(),
            'total_clients' => Client::count(),
            'total_transactions' => Transaction::count(),
            'revenue_today' => Transaction::whereDate('created_at', today())
                ->where('status', 'success')
                ->sum('amount'),
            'revenue_month' => Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'success')
                ->sum('amount'),
            'revenue_total' => Transaction::where('status', 'success')->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
        ];

        // Top 5 propriétaires par revenue
        $topProprios = Proprio::withCount(['wifizones'])
            ->with('wifizones')
            ->get()
            ->map(function ($proprio) {
                $revenue = Transaction::whereIn('wifizone_id', $proprio->wifizones->pluck('id'))
                    ->where('status', 'success')
                    ->sum('amount');
                $proprio->revenue = $revenue;
                return $proprio;
            })
            ->sortByDesc('revenue')
            ->take(5);

        // Revenue des 7 derniers jours (pour graphique)
        $revenueLast7Days = Transaction::where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Répartition par opérateur
        $operatorBreakdown = Transaction::where('status', 'success')
            ->selectRaw('operator, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('operator')
            ->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'topProprios',
            'revenueLast7Days',
            'operatorBreakdown'
        ));
    }

    /**
     * API: Stats temps réel (pour actualisation AJAX)
     */
    public function getRealtimeStats()
    {
        return response()->json([
            'total_proprios' => Proprio::count(),
            'proprios_actifs' => Proprio::where('is_active', true)->count(),
            'revenue_today' => Transaction::whereDate('created_at', today())
                ->where('status', 'success')
                ->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
        ]);
    }

    /**
     * API: Données pour graphique revenue
     */
    public function getRevenueChart()
    {
        $days = request()->get('days', 30);

        $data = Transaction::where('status', 'success')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m')),
            'data' => $data->pluck('total'),
        ]);
    }
}
