<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Liste tous les logs d'audit
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('superAdmin');

        // Filtre par action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filtre par modèle
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        // Filtre par super admin
        if ($request->has('super_admin_id') && $request->super_admin_id) {
            $query->where('super_admin_id', $request->super_admin_id);
        }

        // Filtre par période
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Liste des actions pour le filtre
        $actions = AuditLog::distinct()->pluck('action');

        // Liste des super admins pour le filtre
        $superAdmins = AuditLog::with('superAdmin')
            ->distinct()
            ->whereNotNull('super_admin_id')
            ->get()
            ->pluck('superAdmin.name', 'super_admin_id')
            ->unique();

        return view('superadmin.audit-logs.index', compact('logs', 'actions', 'superAdmins'));
    }

    /**
     * Affiche les détails d'un log
     */
    public function show($id)
    {
        $log = AuditLog::with('superAdmin')->findOrFail($id);
        return view('superadmin.audit-logs.show', compact('log'));
    }

    /**
     * Exporte les logs
     */
    public function export(Request $request, $format)
    {
        $query = AuditLog::with('superAdmin');

        // Appliquer les mêmes filtres que l'index
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'excel') {
            return $this->exportExcel($logs);
        }

        return $this->exportPDF($logs);
    }

    private function exportExcel($logs)
    {
        $rows = [
            ['Date', 'Super Admin', 'Action', 'Modèle', 'ID Modèle', 'Anciennes valeurs', 'Nouvelles valeurs', 'IP']
        ];

        foreach ($logs as $log) {
            $rows[] = [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->superAdmin->name ?? 'N/A',
                $log->action,
                $log->model_type,
                $log->model_id,
                json_encode($log->old_values ?? []),
                json_encode($log->new_values ?? []),
                $log->ip_address,
            ];
        }

        return response()->streamDownload(function() use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'audit_logs.csv');
    }

    private function exportPDF($logs)
    {
        // TODO: Implémenter avec DomPDF
        return response()->download(
            storage_path('exports/audit_logs.pdf'),
            'audit_logs.pdf'
        );
    }
}
