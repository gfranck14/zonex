<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proprio;
use App\Models\Wifizone;
use App\Models\Transaction;
use App\SuperAdmin\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Controller pour la gestion des propriétaires
 */
class ProprioManagementController extends Controller
{
    /**
     * Liste tous les propriétaires
     */
    public function index(Request $request)
    {
        $query = Proprio::query()->withCount('wifizones');

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%");
            });
        }

        // Filtre statut
        if ($request->has('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $proprios = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('superadmin.proprios.index', compact('proprios'));
    }

    /**
     * Affiche les détails d'un propriétaire
     */
    public function show($id)
    {
        $proprio = Proprio::with(['wifizones'])->findOrFail($id);

        // Statistiques du proprio
        $stats = [
            'total_zones' => $proprio->wifizones->count(),
            'total_revenue' => Transaction::whereIn('wifizone_id', $proprio->wifizones->pluck('id'))
                ->where('status', 'success')
                ->sum('amount'),
            'transactions_count' => Transaction::whereIn('wifizone_id', $proprio->wifizones->pluck('id'))->count(),
        ];

        return view('superadmin.proprios.show', compact('proprio', 'stats'));
    }

    /**
     * Active/Désactive un propriétaire
     */
    public function toggleActive($id)
    {
        $proprio = Proprio::findOrFail($id);
        $oldStatus = $proprio->is_active;
        
        $proprio->update(['is_active' => !$proprio->is_active]);

        // Logger l'action
        AuditLog::logAction(
            user: Auth::guard('superadmin')->user(),
            action: $proprio->is_active ? 'activated' : 'deactivated',
            model: 'Proprio',
            oldValues: ['is_active' => $oldStatus],
            newValues: ['is_active' => $proprio->is_active],
            modelId: $proprio->id
        );

        return back()->with('success', 
            $proprio->is_active ? 
            'Propriétaire activé avec succès.' : 
            'Propriétaire désactivé avec succès.'
        );
    }

    /**
     * Impersonation: se connecter en tant que propriétaire
     */
    public function impersonate($id)
    {
        // Seulement pour les GOD
        if (!Auth::guard('superadmin')->user()->isGod()) {
            abort(403, 'Action réservée aux superadmins GOD');
        }

        $proprio = Proprio::findOrFail($id);

        // Sauvegarder l'ID superadmin en session
        session(['impersonating_from_superadmin' => Auth::guard('superadmin')->id()]);

        // Logger l'impersonation
        AuditLog::logAction(
            user: Auth::guard('superadmin')->user(),
            action: 'impersonated',
            model: 'Proprio',
            oldValues: null,
            newValues: ['proprio_id' => $proprio->id],
            modelId: $proprio->id
        );

        // Déconnecter le superadmin
        Auth::guard('superadmin')->logout();

        // Connecter en tant que proprio
        Auth::guard('proprio')->login($proprio);

        return redirect()->route('dashboard')->with('info', 
            '🔓 Vous êtes connecté en tant que ' . $proprio->prenom . ' ' . $proprio->nom
        );
    }

    /**
     * Arrêter l'impersonation
     */
    public function stopImpersonation()
    {
        if (!session()->has('impersonating_from_superadmin')) {
            return redirect()->route('dashboard');
        }

        $superadminId = session('impersonating_from_superadmin');
        session()->forget('impersonating_from_superadmin');

        // Déconnecter le proprio
        Auth::guard('proprio')->logout();

        // Reconnecter le superadmin
        $superadmin = \App\SuperAdmin\Models\SuperAdmin::find($superadminId);
        if ($superadmin) {
            Auth::guard('superadmin')->login($superadmin);
        }

        return redirect()->route('superadmin.dashboard')->with('success', 
            'Impersonation terminée. Vous êtes de retour en mode superadmin.'
        );
    }
}
