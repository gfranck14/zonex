<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller pour l'approbation des retraits
 */
class WithdrawalApprovalController extends Controller
{
    /**
     * Liste toutes les demandes de retrait
     */
    public function index(Request $request)
    {
        // Filtrage par statut
        $status = $request->get('status', 'all');

        // Données statiques pour la démo
        $withdrawals = collect([
            (object)[
                'id' => 1,
                'proprio_nom' => 'Koffi Amani',
                'amount' => 25000,
                'frais' => 500,
                'montant_final' => 24500,
                'phone' => '2250102030405',
                'status' => 'pending',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            (object)[
                'id' => 2,
                'proprio_nom' => 'Diallo Mamadou',
                'amount' => 15000,
                'frais' => 300,
                'montant_final' => 14700,
                'phone' => '2250102030406',
                'status' => 'pending',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
            (object)[
                'id' => 3,
                'proprio_nom' => 'N\'guessan Konan',
                'amount' => 50000,
                'frais' => 1000,
                'montant_final' => 49000,
                'phone' => '2250102030407',
                'status' => 'approved',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            (object)[
                'id' => 4,
                'proprio_nom' => 'Sow Fatou',
                'amount' => 10000,
                'frais' => 200,
                'montant_final' => 9800,
                'phone' => '2250102030408',
                'status' => 'rejected',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            (object)[
                'id' => 5,
                'proprio_nom' => 'Acket Jean',
                'amount' => 30000,
                'frais' => 600,
                'montant_final' => 29400,
                'phone' => '2250102030409',
                'status' => 'paid',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ],
        ]);

        // Filtrer par statut si nécessaire
        if ($status !== 'all') {
            $withdrawals = $withdrawals->where('status', $status);
        }

        // Stats
        $stats = [
            'pending' => $withdrawals->where('status', 'pending')->count(),
            'approved' => $withdrawals->where('status', 'approved')->count(),
            'rejected' => $withdrawals->where('status', 'rejected')->count(),
            'paid' => $withdrawals->where('status', 'paid')->count(),
            'total_pending_amount' => $withdrawals->where('status', 'pending')->sum('amount'),
        ];

        return view('superadmin.super_admin_retraits', compact('withdrawals', 'stats', 'status'));
    }

    /**
     * Approuve une demande de retrait
     */
    public function approve(Request $request, $id)
    {
        return back()->with('success', 'Retrait approuvé avec succès');
    }

    /**
     * Rejete une demande de retrait
     */
    public function reject(Request $request, $id)
    {
        return back()->with('success', 'Retrait rejeté');
    }

    /**
     * Marque comme payé
     */
    public function markAsPaid(Request $request, $id)
    {
        return back()->with('success', 'Retrait marqué comme payé');
    }

    /**
     * Effectue le paiement via l'API
     */
    public function processPayment(Request $request, $id)
    {
        return back()->with('success', 'Paiement effectué avec succès');
    }

    /**
     * Marque le retrait comme payé (avec reference de transaction)
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'transaction_reference' => 'required|string|min:1',
        ]);

        $retrait = \App\Models\Retrait::findOrFail($id);

        if ($retrait->status !== 'pending') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a déjà été traitée. Statut actuel: ' . $retrait->status
                ], 400);
            }
            return back()->withErrors(['error' => 'Cette demande a déjà été traitée.']);
        }

        $retrait->update([
            'status' => 'completed',
            'fedapay_payout_id' => $request->transaction_reference,
        ]);

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
     *批量批准多个提现
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->get('ids', []);
        return back()->with('success', count($ids) . ' retraits approuvés');
    }

    /**
     *批量拒绝多个提现
     */
    public function bulkReject(Request $request)
    {
        $ids = $request->get('ids', []);
        return back()->with('success', count($ids) . ' retraits rejetés');
    }
}
