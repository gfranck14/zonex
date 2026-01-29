<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Wifizone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour la gestion des paiements et des retraits.
 * 
 * Ce contrôleur gère l'affichage des transactions, le calcul des soldes par opérateur
 * et l'accès aux données via des endpoints API.
 */
class PaiementController extends Controller
{
    /**
     * Affiche la page de gestion des paiements et retraits avec les données réelles.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupération des paramètres de filtre
        $filterZone = $request->input('filter_zone');
        $filterStatus = $request->input('filter_status');
        $filterDate = $request->input('filter_date');
        $perPage = $request->input('per_page', 10);

        // Calcul des soldes par opérateur mobile money
        $balances = $this->calculateBalances($user->id);

        // Récupération des transactions filtrées
        $transactions = Transaction::query()
            ->with(['client', 'wifizone', 'ticket']) // Chargement précoce des relations
            // Filtre par zone WiFi
            ->when($filterZone, function($q) use ($filterZone) {
                $q->where('wifizone_id', $filterZone);
            })
            // Filtre par statut de transaction
            ->when($filterStatus, function($q) use ($filterStatus) {
                $q->where('status', $filterStatus);
            })
            // Filtre par date
            ->when($filterDate, function($q) use ($filterDate) {
                $q->whereDate('created_at', $filterDate);
            })
            // Tri par date de création décroissante
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Récupération de toutes les zones pour le filtre dropdown
        $zones = Wifizone::all();

        return view('paiements', compact('balances', 'transactions', 'zones'));
    }

    /**
     * Calcule le solde net par opérateur mobile money.
     * 
     * Le solde est calculé comme: Total des revenus - Total des retraits
     * 
     * @param int $userId ID du propriétaire pour lequel calculer le solde
     * @return array Tableau contenant les soldes par opérateur et le total global
     */
    private function calculateBalances($userId)
    {
        // Calcul du total des revenus par opérateur (transactions réussies)
        $revenueByOperator = Transaction::query()
            ->where('status', 'success')
            ->select('operator', DB::raw('SUM(amount) as total'))
            ->groupBy('operator')
            ->pluck('total', 'operator')
            ->toArray();

        // Calcul du total des retraits par opérateur (retraits complétés ou en cours)
        $withdrawalsByOperator = Withdrawal::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'processing'])
            ->select('operator', DB::raw('SUM(amount) as total'))
            ->groupBy('operator')
            ->pluck('total', 'operator')
            ->toArray();

        // Calcul du solde net pour chaque opérateur
        $operators = ['mtn', 'moov', 'celtiis'];
        $balances = [];
        $totalBalance = 0;

        foreach ($operators as $operator) {
            $revenue = $revenueByOperator[$operator] ?? 0;
            $withdrawals = $withdrawalsByOperator[$operator] ?? 0;
            $balance = $revenue - $withdrawals;
            
            $balances[$operator] = $balance;
            $totalBalance += $balance;
        }

        $balances['total'] = $totalBalance;

        return $balances;
    }

    /**
     * Endpoint API: Récupère les données de solde par opérateur.
     * 
     * Retourne les données au format JSON.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance()
    {
        $user = Auth::user();
        $balances = $this->calculateBalances($user->id);

        return response()->json([
            'success' => true,
            'balances' => $balances,
        ]);
    }

    /**
     * Endpoint API: Récupère les transactions filtrées.
     * 
     * Retourne les données au format JSON avec pagination.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions(Request $request)
    {
        $filterZone = $request->input('filter_zone');
        $filterStatus = $request->input('filter_status');
        $filterDate = $request->input('filter_date');
        $filterOperator = $request->input('filter_operator');
        $perPage = $request->input('per_page', 10);

        $transactions = Transaction::query()
            ->with(['client', 'wifizone', 'ticket'])
            // Filtre par zone WiFi
            ->when($filterZone, function($q) use ($filterZone) {
                $q->where('wifizone_id', $filterZone);
            })
            // Filtre par statut de transaction
            ->when($filterStatus, function($q) use ($filterStatus) {
                $q->where('status', $filterStatus);
            })
            // Filtre par date
            ->when($filterDate, function($q) use ($filterDate) {
                $q->whereDate('created_at', $filterDate);
            })
            // Filtre par opérateur mobile money
            ->when($filterOperator, function($q) use ($filterOperator) {
                $q->where('operator', $filterOperator);
            })
            // Tri par date de création décroissante
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}
