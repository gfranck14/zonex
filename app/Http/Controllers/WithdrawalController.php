<?php

namespace App\Http\Controllers;

use App\Models\Retrait;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur pour la gestion des retraits (Propriétaires).
 * 
 * Ce contrôleur gère les demandes de retrait initiées par les propriétaires,
 * leur historique et les annulations via l'API utilisée par le Wizard.
 */
class WithdrawalController extends Controller
{
    /**
     * Récupère la liste des retraits pour l'utilisateur connecté.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();
        $perPage = $request->input('per_page', 10);

        // Utiliser le modèle Retrait avec la colonne proprio_id
        $withdrawals = Retrait::where('proprio_id', $proprio->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Crée une nouvelle demande de retrait.
     * Note: Pour une intégration complète avec FedaPay, utiliser PayoutController::store()
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:10000000',
            'operator' => 'required|in:mtn,moov,celtiis',
            'phone_number' => 'required|string|max:20',
            'beneficiary_name' => 'required|string|max:255',
            'fedapay_fee' => 'nullable|numeric|min:0',
            'ccorp_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérification de sécurité: le montant demandé ne doit pas dépasser le solde
        if ($request->amount > $proprio->getBalance()) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant pour effectuer ce retrait',
            ], 400);
        }

        // Calcul sécurisé des frais côté serveur (ignorer les envois du client)
        $amount = (float) $request->amount;
        
        $fedapay_fee = 2500;
        if ($amount <= 10000) $fedapay_fee = 150;
        elseif ($amount <= 50000) $fedapay_fee = 300;
        elseif ($amount <= 150000) $fedapay_fee = 800;
        elseif ($amount <= 500000) $fedapay_fee = 2000;

        $ccorp_fee = round($amount * 0.10);

        // Créer le retrait avec le modèle Retrait (nouveaux noms de champs)
        $retrait = Retrait::create([
            'reference' => Retrait::generateReference(),
            'proprio_id' => $proprio->id,
            'amount' => $request->amount,
            'momo_number' => $request->phone_number,
            'momo_name' => $request->beneficiary_name,
            'fedapay_fee' => $fedapay_fee,
            'ccorp_fee' => $ccorp_fee,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande de retrait enregistrée avec succès',
            'withdrawal' => $retrait,
        ], 201);
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

        // Utiliser le modèle Retrait avec proprio_id
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
     * Met à jour le statut d'un retrait (Admin uniquement).
     * 
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:processing,completed,failed',
            'mobile_money_ref' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $retrait = Retrait::findOrFail($id);

        if ($request->status === 'completed') {
            $retrait->markAsProcessed($request->mobile_money_ref);
        } elseif ($request->status === 'processing') {
            $retrait->markAsProcessing();
        } elseif ($request->status === 'failed') {
            $retrait->markAsFailed();
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'withdrawal' => $retrait,
        ]);
    }
}
