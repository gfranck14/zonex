<?php

namespace App\Http\Controllers;

use App\Models\Retrait;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur de compatibilité pour les retraits.
 * 
 * Ce contrôleur utilise désormais le modèle Retrait avec la table 'retraits'.
 * Les anciennes routes API continuent de fonctionner pour la compatibilité.
 * 
 * @deprecated Utiliser PayoutController pour les nouvelles fonctionnalités
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

        // Utiliser le modèle Retrait avec la colonne user_id
        $withdrawals = Retrait::where('user_id', $proprio->id)
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
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Créer le retrait avec le modèle Retrait
        $retrait = Retrait::create([
            'reference' => Retrait::generateReference(),
            'user_id' => $proprio->id,
            'amount' => $request->amount,
            'operator' => $request->operator,
            'mode' => Retrait::getModeFromOperator($request->operator),
            'phone_number' => $request->phone_number,
            'beneficiary_name' => $request->beneficiary_name,
            'status' => 'pending',
            'description' => $request->description ?? 'Retrait ZONEX',
            'merchant_reference' => Retrait::generateMerchantReference(),
            'requested_at' => now(),
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

        // Utiliser le modèle Retrait
        $retrait = Retrait::where('id', $id)
            ->where('user_id', $proprio->id)
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
