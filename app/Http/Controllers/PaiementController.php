<?php

namespace App\Http\Controllers;

use App\Models\Retrait;
use App\Models\Wifizone;
use App\Models\Paiement;
use App\Models\Forfait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour la gestion des paiements et des retraits.
 * 
 * Ce contrôleur gère l'affichage des paiements (achats), le calcul des soldes par opérateur
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

        // Récupérer les IDs des zones WiFi du propriétaire
        $ownerZoneIds = Wifizone::where('proprio_id', $user->id)->pluck('id');

        // Récupération des zones du propriétaire pour le filtre dropdown
        $zones = Wifizone::where('proprio_id', $user->id)->get();

        // Calcul du solde disponible (uniquement paiements réussis - retraits effectués)
        // Si une zone est sélectionnée, calculer le solde pour cette zone uniquement
        $balance = $this->calculateBalance($user->id, $filterZone);

        // Récupération des paiements filtrés (uniquement les zones du propriétaire)
        // On utilise la relation: Paiement -> Forfait -> Wifizone
        $paiements = Paiement::query()
            ->with(['client', 'forfait.wifizone', 'ticket']) // Chargement précoce des relations
            // Filtre obligatoire: uniquement les paiements des zones du propriétaire
            ->whereHas('forfait.wifizone', function($q) use ($ownerZoneIds) {
                $q->whereIn('id', $ownerZoneIds);
            })
            // Filtre par zone WiFi (si sélectionné)
            ->when($filterZone, function($q) use ($filterZone) {
                $q->whereHas('forfait.wifizone', function($q2) use ($filterZone) {
                    $q2->where('id', $filterZone);
                });
            })
            // Filtre par statut de paiement
            ->when($filterStatus, function($q) use ($filterStatus) {
                $q->where('statut', $filterStatus);
            })
            // Filtre par date
            ->when($filterDate, function($q) use ($filterDate) {
                $q->whereDate('created_at', $filterDate);
            })
            // Tri par date de création décroissante
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('paiements', compact('balance', 'paiements', 'zones', 'filterZone'));
    }

    /**
     * Calcule le solde disponible pour un propriétaire.
     * 
     * Le solde est calculé comme: Total des revenus (paiements réussis) - Total des retraits
     * 
     * @param int $userId ID du propriétaire
     * @param int|null $zoneId ID de la zone spécifique (optionnel)
     * @return float Solde disponible
     */
    private function calculateBalance($userId, $zoneId = null)
    {
        // Récupérer les IDs des zones WiFi du propriétaire
        $query = Wifizone::where('proprio_id', $userId);
        
        // Si une zone spécifique est fournie
        if ($zoneId) {
            $query->where('id', $zoneId);
        }
        
        $zoneIds = $query->pluck('id');
        
        if ($zoneIds->isEmpty()) {
            return 0;
        }

        // Calcul du total des revenus (paiements réussis) - uniquement les zones du propriétaire
        $totalRevenue = Paiement::query()
            ->whereHas('forfait.wifizone', function($q) use ($zoneIds) {
                $q->whereIn('id', $zoneIds);
            })
            ->where('statut', 'reussi')
            ->sum('montant');

        // Calcul du total des retraits (retraits complétés ou en cours)
        $totalWithdrawals = Retrait::where('proprio_id', $userId)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('amount');

        return $totalRevenue - $totalWithdrawals;
    }

    /**
     * Endpoint API: Récupère les données de solde.
     * 
     * Retourne les données au format JSON.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance(Request $request)
    {
        $user = Auth::user();
        $filterZone = $request->input('filter_zone');
        $balance = $this->calculateBalance($user->id, $filterZone);

        return response()->json([
            'success' => true,
            'balance' => $balance,
        ]);
    }
}
