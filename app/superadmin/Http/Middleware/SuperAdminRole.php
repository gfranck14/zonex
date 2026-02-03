<?php

namespace App\SuperAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware SuperAdminRole
 * 
 * Vérifie que le superadmin a l'un des rôles requis.
 */
class SuperAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles Rôles autorisés (god, admin, support)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $superadmin = Auth::guard('superadmin')->user();

        // Vérifier si le superadmin a l'un des rôles requis
        if (!$superadmin->hasRole($roles)) {
            abort(403, 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
