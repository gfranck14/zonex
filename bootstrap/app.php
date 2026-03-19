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
