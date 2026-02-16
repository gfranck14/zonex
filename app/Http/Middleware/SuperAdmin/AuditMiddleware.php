<?php

namespace App\Http\Middleware\SuperAdmin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enregistrer l'action dans les logs d'audit
        if (Auth::guard('superadmin')->check()) {
            $this->logAudit($request);
        }

        return $next($request);
    }

    /**
     * Enregistre l'action dans l'audit log
     */
    private function logAudit(Request $request): void
    {
        // Ne pas logger les requêtes API internes ou les lectures GET simples
        if ($request->isMethod('GET') && !$request->is('api/*')) {
            return;
        }

        // Log basique pour le debugging
        AuditLog::create([
            'super_admin_id' => Auth::guard('superadmin')->id(),
            'action' => $this->getActionName($request),
            'model_type' => null,
            'model_id' => null,
            'old_values' => null,
            'new_values' => $this->getRequestData($request),
            'ip_address' => $request->ip(),
        ]);
    }

    /**
     * Obtient le nom de l'action
     */
    private function getActionName(Request $request): string
    {
        $method = strtolower($request->method());
        $route = $request->route();

        if ($route) {
            $action = $route->getAction('as') ?? '';
            return $action ?: "{$method}_request";
        }

        return "{$method}_request";
    }

    /**
     * Extrait les données pertinentes de la requête
     */
    private function getRequestData(Request $request): ?array
    {
        $data = [];

        // Capturer les IDs dans l'URL
        $segments = $request->segments();
        if (count($segments) >= 2) {
            $data['resource_id'] = end($segments);
        }

        // Capturer les données importantes (sans les sensibles)
        $filtered = $request->except(['password', 'token', 'secret', 'api_key']);

        if (!empty($filtered)) {
            $data['input'] = $filtered;
        }

        return empty($data) ? null : $data;
    }
}
