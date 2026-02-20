<?php

namespace App\Http\Controllers;

use App\Models\Retrait;
use App\Models\Proprio;
use App\Models\Paiement;
use App\Models\Wifizone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur simplifié pour la gestion des retraits.
 * 
 * Ce contrôleur enregistre uniquement les demandes de retrait en base de données.
 * L'envoi effectif des fonds via FedaPay est géré manuellement par l'administrateur.
 */
class PayoutController extends Controller
{
    /**
     * Affiche la page de gestion des retraits.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();
        
        // Récupération des paramètres de filtre
        $filterStatus = $request->input('filter_status');
        $filterDate = $request->input('filter_date');
        $perPage = $request->input('per_page', 10);

        // Récupération des retraits filtrés
        $retraitsQuery = Retrait::query()
            ->where('proprio_id', $proprio->id)
            ->when($filterStatus, function($q) use ($filterStatus) {
                $q->where('status', $filterStatus);
            })
            ->when($filterDate, function($q) use ($filterDate) {
                $q->whereDate('created_at', $filterDate);
            })
            ->orderBy('created_at', 'desc');

        $retraits = $retraitsQuery->paginate($perPage);

        // Calcul du solde disponible
        $balance = $this->calculateBalance($proprio->id);

        return view('retraits', compact('retraits', 'balance', 'proprio'));
    }

    /**
     * Crée une nouvelle demande de retrait.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation des données
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:10000000',
            'momo_number' => 'required|string|max:20',
            'momo_name' => 'required|string|max:255',
            'fedapay_fee' => 'nullable|numeric|min:0',
            'ccorp_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérifier le solde disponible
        $balance = $this->calculateBalance($proprio->id);

        if ($balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant',
                'available_balance' => $balance,
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Générer la référence
            $reference = Retrait::generateReference();

            // Créer l'enregistrement en base
            $retrait = Retrait::create([
                'reference' => $reference,
                'proprio_id' => $proprio->id,
                'amount' => $request->amount,
                'momo_number' => $request->momo_number,
                'momo_name' => $request->momo_name,
                'fedapay_fee' => $request->fedapay_fee ?? 0,
                'ccorp_fee' => $request->ccorp_fee ?? 0,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande de retrait enregistrée avec succès',
                'retrait' => $retrait->load('proprio'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[PayoutController] Erreur création retrait', [
                'error' => $e->getMessage(),
                'proprio_id' => $proprio->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du retrait',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Annule une demande de retrait en attente.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        $proprio = Auth::guard('proprio')->user();

        $retrait = Retrait::where('id', $id)
            ->where('proprio_id', $proprio->id)
            ->first();

        if (!$retrait) {
            return response()->json([
                'success' => false,
                'message' => 'Retrait introuvable',
            ], 404);
        }

        if (!$retrait->isCancellable()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce retrait ne peut plus être annulé',
            ], 400);
        }

        $retrait->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Retrait annulé avec succès',
            'withdrawal' => $retrait,
        ]);
    }

    /**
     * Calcule le solde disponible du propriétaire.
     * 
     * @param int $proprioId
     * @return float
     */
    private function calculateBalance($proprioId)
    {
        // Calculer le total des paiements reçus par ce propriétaire
        $totalPayments = Paiement::where('proprietaire_id', $proprioId)
            ->where('statut', 'approuve')
            ->sum('montant');

        // Calculer le total des retraits effectués (incluant les frais)
        $totalWithdrawals = Retrait::where('proprio_id', $proprioId)
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->value('total_amount');

        return $totalPayments - $totalWithdrawals;
    }

    /**
     * Formate un numéro de téléphone selon le pays.
     * 
     * @param string $phoneNumber
     * @param string $country
     * @return string
     */
    private function formatPhoneNumber($phoneNumber, $country = 'bj')
    {
        // Enlever tous les caractères non numériques
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Ajouter l'indicatif du pays si nécessaire
        if (strlen($phoneNumber) === 10) {
            if ($country === 'bj') {
                return '229' . $phoneNumber;
            }
        }

        return $phoneNumber;
    }

    /**
     * Détecte le pays à partir du numéro de téléphone.
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function getCountryFromPhone($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 2) === '01') {
            return 'bj'; // Bénin
        }

        return 'bj'; // Par défaut Bénin
    }
}
