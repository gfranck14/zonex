<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\WifiZone;
use App\Models\Forfait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour le dashboard du propriétaire.
 * 
 * Ce contrôleur gère l'affichage du dashboard principal avec les KPIs (Key Performance Indicators)
 * et les données dynamiques relatives aux ventes, au stock et aux zones WiFi.
 */
class DashboardController extends Controller
{
    /**
     * Affiche le dashboard propriétaire avec des données dynamiques.
     * 
     * Ce dashboard contient:
     * - Les KPIs (Tickets vendus aujourd'hui, Revenu du jour, Stock total disponible, Forfait le plus vendu)
     * - Un graphique des ventes des 7 derniers jours
     * - La liste des zones WiFi avec leur stock disponible
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $proprioId = Auth::guard('proprio')->id();
        $zoneId = $request->get('zone_id');

        // 1. Liste des zones WiFi du propriétaire (pour le sélecteur de zone)
        $zones = WifiZone::where('proprio_id', $proprioId)->get();

        // 2. Zone WiFi actuelle (pour le filtrage des données)
        $currentZone = null;
        if ($zoneId) {
            $currentZone = WifiZone::where('id', $zoneId)->where('proprio_id', $proprioId)->first();
        }

        // =====================================================================
        // 3. CALCUL DES KPI (Key Performance Indicators)
        // =====================================================================

        // --- Tickets actifs vendus aujourd'hui ---
        $ticketsActifsQuery = Ticket::where('statut', 'vendu')
            ->whereDate('date_vente', Carbon::today());
        
        if ($currentZone) {
            $ticketsActifsQuery->whereHas('forfait', function($q) use ($currentZone) {
                $q->where('wifizones_id', $currentZone->id);
            });
        } else {
            $ticketsActifsQuery->whereHas('forfait', function($q) use ($proprioId) {
                $q->whereHas('wifizone', function($qz) use ($proprioId) {
                    $qz->where('proprio_id', $proprioId);
                });
            });
        }
        $ticketsActifsCount = $ticketsActifsQuery->count();

        // --- Revenu total du jour ---
        $revenuJourQuery = Transaction::successful()
            ->whereDate('created_at', Carbon::today());
        
        if ($currentZone) {
            $revenuJourQuery->where('wifizone_id', $currentZone->id);
        } else {
            $revenuJourQuery->whereHas('wifizone', function($q) use ($proprioId) {
                $q->where('proprio_id', $proprioId);
            });
        }
        $revenuJour = $revenuJourQuery->sum('amount');

        // --- Stock total disponible (tickets libres) ---
        $stockTotalQuery = Ticket::where('statut', 'libre');
        if ($currentZone) {
            $stockTotalQuery->whereHas('forfait', function($q) use ($currentZone) {
                $q->where('wifizones_id', $currentZone->id);
            });
        } else {
            $stockTotalQuery->whereHas('forfait', function($q) use ($proprioId) {
                $q->whereHas('wifizone', function($qz) use ($proprioId) {
                    $qz->where('proprio_id', $proprioId);
                });
            });
        }
        $stockTotal = $stockTotalQuery->count();

        // --- Forfait le plus vendu aujourd'hui ---
        $topForfaitQuery = DB::table('tickets')
            ->join('forfaits', 'tickets.forfaits_id', '=', 'forfaits.id')
            ->select('forfaits.nom', DB::raw('count(*) as total'))
            ->where('tickets.statut', 'vendu')
            ->whereDate('tickets.date_vente', Carbon::today());

        if ($currentZone) {
            $topForfaitQuery->where('forfaits.wifizones_id', $currentZone->id);
        } else {
            $topForfaitQuery->join('wifizones', 'forfaits.wifizones_id', '=', 'wifizones.id')
                ->where('wifizones.proprio_id', $proprioId);
        }

        $topForfait = $topForfaitQuery->groupBy('forfaits.id', 'forfaits.nom')
            ->orderBy('total', 'desc')
            ->first();

        // =====================================================================
        // 4. DONNÉES POUR LE GRAPHIQUE DES 7 DERNIERS JOURS
        // =====================================================================
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $query = Transaction::successful()->whereDate('created_at', $date);
            if ($currentZone) {
                $query->where('wifizone_id', $currentZone->id);
            } else {
                $query->whereHas('wifizone', function($q) use ($proprioId) {
                    $q->where('proprio_id', $proprioId);
                });
            }
            $salesData[] = [
                'day' => $date->translatedFormat('D'),
                'amount' => $query->sum('amount')
            ];
        }

        // =====================================================================
        // 5. STOCK PAR ZONE WIFI (Pour la liste à droite)
        // =====================================================================
        $zonesStocks = WifiZone::where('proprio_id', $proprioId)
            ->withCount(['forfaits as tickets_count' => function($q) {
                $q->join('tickets', 'forfaits.id', '=', 'tickets.forfaits_id')
                  ->where('tickets.statut', 'libre')
                  ->select(DB::raw("count(tickets.id)"));
            }])
            ->get();

        // =====================================================================
        // 6. STATUT DU STOCK PAR ZONE (Standardisé avec ForfaitController)
        // =====================================================================
        $zoneStockStatus = [];
        foreach ($zonesStocks as $zone) {
            $ticketsCount = $zone->tickets_count ?? 0;
            
            if ($ticketsCount == 0) {
                $status = [
                    'status' => 'empty',
                    'icon' => 'fa-times-circle',
                    'color' => 'text-red-500'
                ];
            } elseif ($ticketsCount <= 20) {
                $status = [
                    'status' => 'low',
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'text-orange-500'
                ];
            } else {
                $status = [
                    'status' => 'good',
                    'icon' => 'fa-check-circle',
                    'color' => 'text-green-500'
                ];
            }
            $zoneStockStatus[$zone->id] = $status;
        }

        // =====================================================================
        // RENDU DE LA VUE
        // =====================================================================
        return view('proprio.index_proprio', compact(
            'zones', 
            'currentZone', 
            'ticketsActifsCount',
            'revenuJour',
            'stockTotal',
            'topForfait',
            'salesData',
            'zonesStocks',
            'zoneStockStatus'
        ));
    }
}
