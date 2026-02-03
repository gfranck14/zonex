<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Routes superadmin
            Route::middleware('web')
                ->group(base_path('routes/superadmin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enregistrer les alias de middleware superadmin
        $middleware->alias([
            'superadmin.auth' => \App\SuperAdmin\Http\Middleware\SuperAdminAuth::class,
            'superadmin.role' => \App\SuperAdmin\Http\Middleware\SuperAdminRole::class,
            'superadmin.audit' => \App\SuperAdmin\Http\Middleware\AuditMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
