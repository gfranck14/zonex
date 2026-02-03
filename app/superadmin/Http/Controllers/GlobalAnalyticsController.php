<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proprio;
use App\Models\Wifizone;
use App\Models\Transaction;
use Illuminate\Http\Request;

/**
 * Controller pour les analytics globaux
 */
class GlobalAnalyticsController extends Controller
{
    /**
     * Page analytics principale
     */
    public function index()
    {
        return view('superadmin.analytics.index');
    }

    /**
     * API: Performance des propriétaires
     */
    public function getProprioPerformance()
    {
        $proprios = Proprio::with('wifizones')->get()->map(function ($proprio) {
            $revenue = Transaction::whereIn('wifizone_id', $proprio->wifizones->pluck('id'))
                ->where('status', 'success')
                ->sum('amount');
            
            return [
                'id' => $proprio->id,
                'name' => $proprio->prenom . ' ' . $proprio->nom,
                'zones_count' => $proprio->wifizones->count(),
                'revenue' => $revenue,
                'transactions_count' => Transaction::whereIn('wifizone_id', $proprio->wifizones->pluck('id'))->count(),
            ];
        })->sortByDesc('revenue')->values();

        return response()->json($proprios);
    }

    /**
     * API: Stats par zone
     */
    public function getZoneAnalytics()
    {
        $zones = Wifizone::with('proprio')->get()->map(function ($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->nom_zone,
                'proprio' => $zone->proprio->prenom . ' ' . $zone->proprio->nom,
                'revenue' => Transaction::where('wifizone_id', $zone->id)
                    ->where('status', 'success')
                    ->sum('amount'),
                'transactions' => Transaction::where('wifizone_id', $zone->id)->count(),
            ];
        })->sortByDesc('revenue')->values();

        return response()->json($zones);
    }

    /**
     * API: Répartition par opérateur
     */
    public function getOperatorBreakdown()
    {
        $data = Transaction::where('status', 'success')
            ->selectRaw('operator, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('operator')
            ->get();

        return response()->json($data);
    }

    /**
     * Export rapports (placeholder)
     */
    public function exportReport($format)
    {
        // TODO: Implémenter export Excel/PDF
        return back()->with('info', 'Export en cours de développement.');
    }
}
