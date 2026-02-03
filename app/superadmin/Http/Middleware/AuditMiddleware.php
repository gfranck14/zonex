<?php

namespace App\SuperAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\SuperAdmin\Models\AuditLog;

/**
 * Middleware AuditMiddleware
 * 
 * Enregistre automatiquement les actions sensibles dans les logs d'audit.
 */
class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Exécuter la requête
        $response = $next($request);

        // Enregistrer les actions sensibles (POST, PUT, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $this->logAction($request);
        }

        return $response;
    }

    /**
     * Enregistre l'action dans les logs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function logAction(Request $request): void
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        if (!$superadmin) {
            return;
        }

        // Déterminer l'action basée sur la méthode et la route
        $action = $this->determineAction($request);
        
        // Ne logger que les actions importantes
        $importantPaths = [
            'proprios',
            'withdrawals',
            'tickets',
            'settings',
            'impersonate',
        ];

        $shouldLog = false;
        foreach ($importantPaths as $path) {
            if (str_contains($request->path(), $path)) {
                $shouldLog = true;
                break;
            }
        }

        if ($shouldLog) {
            AuditLog::logAction(
                user: $superadmin,
                action: $action,
                model: $this->extractModel($request),
                oldValues: null, // Sera rempli dans les contrôleurs si nécessaire
                newValues: $request->all(),
                modelId: $this->extractModelId($request)
            );
        }
    }

    /**
     * Détermine le type d'action
     */
    protected function determineAction(Request $request): string
    {
        return match($request->method()) {
            'POST' => str_contains($request->path(), 'approve') ? 'approved' : 
                      (str_contains($request->path(), 'reject') ? 'rejected' :
                      (str_contains($request->path(), 'impersonate') ? 'impersonated' :
                      'created')),
            'PUT' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };
    }

    /**
     * Extrait le nom du modèle de la route
     */
    protected function extractModel(Request $request): ?string
    {
        $path = $request->path();
        
        if (str_contains($path, 'proprios')) return 'Proprio';
        if (str_contains($path, 'withdrawals')) return 'Withdrawal';
        if (str_contains($path, 'tickets')) return 'SupportTicket';
        if (str_contains($path, 'settings')) return 'SystemConfig';
        
        return null;
    }

    /**
     * Extrait l'ID du modèle de la route
     */
    protected function extractModelId(Request $request): ?int
    {
        // Chercher un ID dans les segments de la route
        $segments = $request->segments();
        foreach ($segments as $segment) {
            if (is_numeric($segment)) {
                return (int) $segment;
            }
        }
        
        return null;
    }
}
