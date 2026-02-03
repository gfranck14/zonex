<?php

namespace App\SuperAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware SuperAdminAuth
 * 
 * Vérifie que l'utilisateur est authentifié en tant que superadmin.
 */
class SuperAdminAuth
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
        // Vérifier si l'utilisateur est authentifié via le guard superadmin
        if (!Auth::guard('superadmin')->check()) {
            // Rediriger vers la page de login
            return redirect()->route('superadmin.login')
                ->with('error', 'Vous devez être connecté en tant qu\'administrateur.');
        }

        // Vérifier si le compte est actif
        $superadmin = Auth::guard('superadmin')->user();
        if (!$superadmin->is_active) {
            Auth::guard('superadmin')->logout();
            return redirect()->route('superadmin.login')
                ->with('error', 'Votre compte administrateur est désactivé.');
        }

        return $next($request);
    }
}
