<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Retrait;
use App\Models\Proprio;
use App\SuperAdmin\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller pour la gestion des retraits (SuperAdmin).
 * 
 * Ce controller utilise le modèle Retrait avec la table 'retraits'.
 * Logger toutes les actions pour le suivi du processus.
 */
class WithdrawalApprovalController extends Controller
{
    /**
     * Liste des retraits.
     */
    public function index(Request $request)
    {
        Log::info('[Withdrawal] Liste des retraits demandée', [
            'user_id' => Auth::guard('superadmin')->id(),
            'filter_status' => $request->get('status', 'all'),
        ]);
        
        $query = Retrait::with('proprio')->orderBy('created_at', 'desc');

        // Filtre par statut
        $status = $request->get('status', 'pending');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate(20);
        
        Log::info('[Withdrawal] Nombre de retraits récupérés', [
            'count' => $withdrawals->total(),
        ]);
        
        return view('superadmin.super_admin_retraits', compact('withdrawals', 'status'));
    }

    /**
     * Détails d'une demande de retrait.
     */
    public function show($id)
    {
        Log::info('[Withdrawal] Détails du retrait demandés', [
            'retrait_id' => $id,
            'user_id' => Auth::guard('superadmin')->id(),
        ]);
        
        $withdrawal = Retrait::with('proprio')->findOrFail($id);
        
        Log::info('[Withdrawal] Retrait trouvé', [
            'retrait_id' => $id,
            'amount' => $withdrawal->amount,
            'status' => $withdrawal->status,
        ]);
        
        return view('superadmin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Enregistrer le paiement d'un retrait.
     */
    public function pay(Request $request, $id)
    {
        Log::info('[Withdrawal] Début enregistrement paiement', [
            'retrait_id' => $id,
            'user_id' => Auth::guard('superadmin')->id(),
            'transaction_reference' => $request->transaction_reference ?? 'non fournie',
        ]);
        
        $request->validate([
            'transaction_reference' => 'required|string|min:1',
        ]);

        $retrait = Retrait::findOrFail($id);
        
        Log::info('[Withdrawal] Retrait trouvé pour paiement', [
            'retrait_id' => $id,
            'amount' => $retrait->amount,
            'current_status' => $retrait->status,
        ]);

        if ($retrait->status !== 'pending') {
            Log::warning('[Withdrawal] Tentative de paiement sur retrait déjà traité', [
                'retrait_id' => $id,
                'status' => $retrait->status,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a déjà été traitée. Statut actuel: ' . $retrait->status
                ], 400);
            }
            return back()->withErrors(['error' => 'Cette demande a déjà été traitée.']);
        }

        $superadmin = Auth::guard('superadmin')->user();
        
        Log::info('[Withdrawal] SuperAdmin identifié', [
            'superadmin_id' => $superadmin->id,
            'superadmin_email' => $superadmin->email,
        ]);

        // Enregistrer le paiement : changer le statut et sauvegarder la reference
        $retrait->update([
            'status' => 'completed',
            'fedapay_payout_id' => $request->transaction_reference,
            'processed_at' => now(),
        ]);
        
        Log::info('[Withdrawal] Paiement enregistré avec succès', [
            'retrait_id' => $id,
            'amount' => $retrait->amount,
            'transaction_reference' => $request->transaction_reference,
            'processed_at' => now(),
        ]);

        // Logger l'action
        AuditLog::logAction(
            user: $superadmin,
            action: 'paid',
            model: 'Retrait',
            oldValues: ['status' => 'pending'],
            newValues: [
                'status' => 'completed',
                'amount' => $retrait->amount,
                'transaction_reference' => $request->transaction_reference,
            ],
            modelId: $retrait->id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès.',
                'retrait' => $retrait,
            ]);
        }

        return back()->with('success', 
            'Paiement enregistré. Reference: ' . $request->transaction_reference
        );
    }

    /**
     * Rejeter une demande de retrait.
     */
    public function reject(Request $request, $id)
    {
        Log::info('[Withdrawal] Début rejet retrait', [
            'retrait_id' => $id,
            'user_id' => Auth::guard('superadmin')->id(),
        ]);
        
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $retrait = Retrait::findOrFail($id);
        
        Log::info('[Withdrawal] Retrait trouvé pour rejet', [
            'retrait_id' => $id,
            'amount' => $retrait->amount,
            'current_status' => $retrait->status,
        ]);

        if ($retrait->status !== 'pending') {
            Log::warning('[Withdrawal] Tentative de rejet sur retrait déjà traité', [
                'retrait_id' => $id,
                'status' => $retrait->status,
            ]);
            
            return back()->withErrors(['error' => 'Cette demande a déjà été traitée.']);
        }

        $superadmin = Auth::guard('superadmin')->user();

        $retrait->update([
            'status' => 'cancelled',
            'fedapay_error_message' => $request->rejection_reason,
            'processed_at' => now(),
        ]);
        
        Log::info('[Withdrawal] Retrait rejeté', [
            'retrait_id' => $id,
            'reason' => $request->rejection_reason,
        ]);

        // Logger le rejet
        AuditLog::logAction(
            user: $superadmin,
            action: 'rejected',
            model: 'Retrait',
            oldValues: ['status' => 'pending'],
            newValues: [
                'status' => 'cancelled',
                'rejection_reason' => $request->rejection_reason,
            ],
            modelId: $retrait->id
        );

        return back()->with('success', 'Retrait rejeté. Le propriétaire en sera notifié.');
    }
}
