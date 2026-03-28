<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Personnaliser la redirection des utilisateurs non-authentifiés (corrige l'erreur Route [login] not defined)
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('super-admin*')) {
                return route('super_admin.login');
            }
            if ($request->is('portal*') || $request->is('client*')) {
                return route('client.landing.default');
            }
            return route('proprio.login');
        });

        // Personnaliser la redirection des utilisateurs DÉJÀ authentifiés (quand ils visitent /login)
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if (\Illuminate\Support\Facades\Auth::guard('superadmin')->check()) {
                return route('super_admin.dashboard');
            }
            if (\Illuminate\Support\Facades\Auth::guard('client')->check()) {
                // Pour le client on pourrait utiliser 'client.shop' ou '/portal' mais on retourne une redirection sécure
                return '/portal';
            }
            // Par défaut, rediriger le propriétaire sur son dashboard
            return route('proprio.dashboard');
        });

        // Enregistrer les alias de middleware superadmin
        $middleware->alias([
            'superadmin.auth' => \App\Http\Middleware\SuperAdmin\SuperAdminAuth::class,
            'superadmin.role' => \App\Http\Middleware\SuperAdmin\SuperAdminRole::class,
            'superadmin.audit' => \App\Http\Middleware\SuperAdmin\AuditMiddleware::class,
            'track.client.session' => \App\Http\Middleware\TrackClientSession::class,
            'redirect.if.not.client' => \App\Http\Middleware\RedirectIfNotClient::class,
        ]);
        
        // Ajouter le middleware de suivi aux routes web
        $middleware->web(\App\Http\Middleware\TrackClientSession::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
