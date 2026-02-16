<?php

namespace App\Http\Middleware\SuperAdmin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.login')
                ->with('error', 'Veuillez vous connecter');
        }

        $user = Auth::guard('superadmin')->user();

        // Si aucun rôle n'est spécifié, juste vérifier l'authentification
        if (empty($roles)) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a au moins un des rôles spécifiés
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Rôle insuffisant'], 403);
            }

            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour cette action');
        }

        return $next($request);
    }
}
