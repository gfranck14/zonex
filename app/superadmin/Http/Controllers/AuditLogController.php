<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\SuperAdmin\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * Controller pour consulter les logs d'audit
 */
class AuditLogController extends Controller
{
    /**
     * Liste des logs d'audit
     */
    public function index(Request $request)
    {
        $query = AuditLog::orderBy('created_at', 'desc');

        // Filtre par type d'utilisateur
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filtre par action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filtre par modèle
        if ($request->has('model')) {
            $query->where('model', $request->model);
        }

        // Filtre par date
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Liste des actions uniques pour le filtre
        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $models = AuditLog::select('model')->distinct()->whereNotNull('model')->pluck('model');

        return view('superadmin.audit-logs.index', compact('logs', 'actions', 'models'));
    }

    /**
     * Détails d'un log
     */
    public function show($id)
    {
        $log = AuditLog::findOrFail($id);
        
        return view('superadmin.audit-logs.show', compact('log'));
    }

    /**
     * Export des logs (placeholder)
     */
    public function export($format)
    {
        // TODO: Implémenter export Excel/PDF
        return back()->with('info', 'Fonctionnalité d\'export en cours de développement.');
    }
}
