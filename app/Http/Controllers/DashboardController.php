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
        // 3. CALCUL DES KPI MULTI-PÉRIODES (Année, Mois, Jour)
        // =====================================================================
        
        // Base query pour les paiements réussis du propriétaire
        $basePaiementQuery = \App\Models\Paiement::where('paiements.statut', 'reussi');
        
        if ($currentZone) {
            $basePaiementQuery->whereHas('forfait', function($q) use ($currentZone) {
                $q->where('wifizones_id', $currentZone->id);
            });
        } else {
            $basePaiementQuery->whereHas('forfait', function($q) use ($proprioId) {
                $q->whereHas('wifizone', function($qz) use ($proprioId) {
                    $qz->where('proprio_id', $proprioId);
                });
            });
        }

        // --- Définition des périodes ---
        $now = Carbon::now();
        $periods = [
            'day'   => $now->copy()->startOfDay(),
            'month' => $now->copy()->startOfMonth(),
            'year'  => $now->copy()->startOfYear(),
        ];

        $kpiData = [
            'sales' => [],
            'revenue' => [],
            'top' => [],
            'meta' => [
                'day'   => 'Aujourd\'hui',
                'month' => $now->translatedFormat('F'),
                'year'  => $now->year,
            ]
        ];

        foreach ($periods as $key => $startDate) {
            $periodQuery = (clone $basePaiementQuery)->where('paiements.created_at', '>=', $startDate);
            
            // 1. Ventes (Count)
            $kpiData['sales'][$key] = $periodQuery->count();
            
            // 2. Revenu (Sum)
            $kpiData['revenue'][$key] = $periodQuery->sum('montant');
            
            // 3. Top Forfait
            $top = (clone $periodQuery)
                ->join('forfaits', 'paiements.forfait_id', '=', 'forfaits.id')
                ->select('forfaits.nom', DB::raw('count(*) as total'))
                ->groupBy('forfaits.id', 'forfaits.nom')
                ->orderBy('total', 'desc')
                ->first();
            
            $kpiData['top'][$key] = $top ? [
                'nom' => $top->nom,
                'total' => $top->total
            ] : [
                'nom' => 'Aucun',
                'total' => 0
            ];
        }

        // --- Stock total disponible (KPI statique) ---
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

        // =====================================================================
        // 4. DONNÉES POUR LES GRAPHIQUES (Multi-Périodes & Multi-Métriques)
        // =====================================================================
        $kpiData['charts'] = [
            'year' => [
                'labels'  => ['Janv', 'Févr', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
                'volume'  => array_fill(0, 12, 0),
                'revenue' => array_fill(0, 12, 0),
            ],
            'month' => [
                'labels'  => range(1, $now->daysInMonth),
                'volume'  => array_fill(0, $now->daysInMonth, 0),
                'revenue' => array_fill(0, $now->daysInMonth, 0),
            ],
            'day' => [
                'labels'  => array_map(fn($h) => $h.'h', range(0, 23)),
                'volume'  => array_fill(0, 24, 0),
                'revenue' => array_fill(0, 24, 0),
            ]
        ];

        // --- Remplissage Année (12 mois) ---
        $yearStats = (clone $basePaiementQuery)
            ->whereYear('paiements.created_at', $now->year)
            ->selectRaw('MONTH(paiements.created_at) as m, COUNT(*) as v, SUM(montant) as r')
            ->groupBy('m')->get();
        foreach ($yearStats as $s) {
            $kpiData['charts']['year']['volume'][$s->m - 1] = (int)$s->v;
            $kpiData['charts']['year']['revenue'][$s->m - 1] = (float)$s->r;
        }

        // --- Remplissage Mois (jours du mois) ---
        $monthStats = (clone $basePaiementQuery)
            ->whereYear('paiements.created_at', $now->year)
            ->whereMonth('paiements.created_at', $now->month)
            ->selectRaw('DAY(paiements.created_at) as d, COUNT(*) as v, SUM(montant) as r')
            ->groupBy('d')->get();
        foreach ($monthStats as $s) {
            $kpiData['charts']['month']['volume'][$s->d - 1] = (int)$s->v;
            $kpiData['charts']['month']['revenue'][$s->d - 1] = (float)$s->r;
        }

        // --- Remplissage Jour (24 heures) ---
        $dayStats = (clone $basePaiementQuery)
            ->whereDate('paiements.created_at', $now->toDateString())
            ->selectRaw('HOUR(paiements.created_at) as h, COUNT(*) as v, SUM(montant) as r')
            ->groupBy('h')->get();
        foreach ($dayStats as $s) {
            $kpiData['charts']['day']['volume'][$s->h] = (int)$s->v;
            $kpiData['charts']['day']['revenue'][$s->h] = (float)$s->r;
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
            'kpiData',
            'stockTotal',
            'zonesStocks',
            'zoneStockStatus'
        ));
    }
}
