<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Proprio;
use App\SuperAdmin\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller pour la validation des retraits
 */
class WithdrawalApprovalController extends Controller
{
    /**
     * Liste des retraits en attente d'approbation
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('proprio')->orderBy('created_at', 'desc');

        // Filtre par statut
        $status = $request->get('status', 'pending');
        $query->where('status', $status);

        $withdrawals = $query->paginate(20);
        
        return view('superadmin.withdrawals.index', compact('withdrawals', 'status'));
    }

    /**
     * Détails d'une demande de retrait
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::with('proprio')->findOrFail($id);
        
        return view('superadmin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approuver une demande de retrait
     */
    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['error' => 'Cette demande a déjà été traitée.']);
        }

        $superadmin = Auth::guard('superadmin')->user();

        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => $superadmin->id,
            'approved_at' => now(),
        ]);

        // Logger l'approbation
        AuditLog::logAction(
            user: $superadmin,
            action: 'approved',
            model: 'Withdrawal',
            oldValues: ['status' => 'pending'],
            newValues: [
                'status' => 'approved',
                'approved_by' => $superadmin->id,
                'amount' => $withdrawal->amount,
            ],
            modelId: $withdrawal->id
        );

        return back()->with('success', 
            'Retrait approuvé avec succès. Montant: ' . number_format($withdrawal->amount, 0, ',', ' ') . ' FCFA'
        );
    }

    /**
     * Rejeter une demande de retrait
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['error' => 'Cette demande a déjà été traitée.']);
        }

        $superadmin = Auth::guard('superadmin')->user();

        $withdrawal->update([
            'status' => 'rejected',
            'approved_by' => $superadmin->id,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Logger le rejet
        AuditLog::logAction(
            user: $superadmin,
            action: 'rejected',
            model: 'Withdrawal',
            oldValues: ['status' => 'pending'],
            newValues: [
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ],
            modelId: $withdrawal->id
        );

        return back()->with('success', 'Retrait rejeté. Le propriétaire en sera notifié.');
    }
}
