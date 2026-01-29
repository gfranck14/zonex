<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    /**
     * Store a new withdrawal request
     */
    public function store(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1000|max:1000000',
            'operator' => 'required|in:mtn,moov,celtiis',
            'phone_number' => 'required|string|max:20',
            'beneficiary_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check available balance for the selected operator
        $balance = $this->getOperatorBalance($proprio->id, $request->operator);

        if ($balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant pour cet opérateur',
                'available_balance' => $balance,
            ], 400);
        }

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'reference' => Withdrawal::generateReference(),
            'proprio_id' => $proprio->id,
            'amount' => $request->amount,
            'operator' => $request->operator,
            'phone_number' => $request->phone_number,
            'beneficiary_name' => $request->beneficiary_name,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande de retrait enregistrée avec succès',
            'withdrawal' => $withdrawal,
        ], 201);
    }

    /**
     * Get withdrawal history for current user
     */
    public function index(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();
        $perPage = $request->input('per_page', 10);

        $withdrawals = Withdrawal::query()
            ->where('proprio_id', $proprio->id)
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Cancel a pending withdrawal
     */
    public function cancel($id)
    {
        $proprio = Auth::guard('proprio')->user();

        $withdrawal = Withdrawal::where('id', $id)
            ->where('proprio_id', $proprio->id)
            ->first();

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Retrait introuvable',
            ], 404);
        }

        if (!$withdrawal->isCancellable()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce retrait ne peut plus être annulé',
            ], 400);
        }

        $withdrawal->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Retrait annulé avec succès',
            'withdrawal' => $withdrawal,
        ]);
    }

    /**
     * Update withdrawal status (Admin only)
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

        $withdrawal = Withdrawal::findOrFail($id);

        if ($request->status === 'completed') {
            $withdrawal->markAsProcessed($request->mobile_money_ref);
        } elseif ($request->status === 'processing') {
            $withdrawal->markAsProcessing();
        } elseif ($request->status === 'failed') {
            $withdrawal->markAsFailed();
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'withdrawal' => $withdrawal,
        ]);
    }

    /**
     * Calculate available balance for a specific operator
     */
    private function getOperatorBalance($proprioId, $operator)
    {
        // Revenue from successful transactions
        $revenue = Transaction::where('operator', $operator)
            ->where('status', 'success')
            ->sum('amount');

        // Withdrawals (completed + processing)
        $withdrawals = Withdrawal::where('proprio_id', $proprioId)
            ->where('operator', $operator)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('amount');

        return $revenue - $withdrawals;
    }
}
