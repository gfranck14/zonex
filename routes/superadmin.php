<?php

use Illuminate\Support\Facades\Route;
use App\SuperAdmin\Http\Controllers\SuperAdminAuthController;
use App\SuperAdmin\Http\Controllers\SuperAdminDashboardController;
use App\SuperAdmin\Http\Controllers\ProprioManagementController;
use App\SuperAdmin\Http\Controllers\GlobalAnalyticsController;
use App\SuperAdmin\Http\Controllers\WithdrawalApprovalController;
use App\SuperAdmin\Http\Controllers\SupportTicketController;
use App\SuperAdmin\Http\Controllers\SystemConfigController;
use App\SuperAdmin\Http\Controllers\AuditLogController;

// =============================================================================
// ROUTES SUPERADMIN (GOD)
// =============================================================================
// Toutes les routes sont préfixées par /god-admin
// Accès réservé aux superadministrateurs avec privilèges élevés

Route::prefix('god-admin')->name('superadmin.')->group(function () {
    
    // -------------------------------------------------------------------------
    // AUTHENTIFICATION (Guest)
    // -------------------------------------------------------------------------
    Route::middleware('guest:superadmin')->group(function () {
        
        // Page de login
        Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])
            ->name('login');
        
        // Traitement login
        Route::post('/login', [SuperAdminAuthController::class, 'login'])
            ->name('login.submit');
        
        // Vérification 2FA
        Route::post('/verify-2fa', [SuperAdminAuthController::class, 'verify2FA'])
            ->name('verify2fa');
    });

    // -------------------------------------------------------------------------
    // ROUTES PROTÉGÉES (Authenticated + Audit)
    // -------------------------------------------------------------------------
    Route::middleware(['auth:superadmin', 'superadmin.audit'])->group(function () {
        
        // Dashboard principal
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])
            ->name('dashboard');
        
        // API: Stats temps réel
        Route::get('/api/realtime-stats', [SuperAdminDashboardController::class, 'getRealtimeStats'])
            ->name('api.realtime-stats');
        
        // API: Graphique revenue
        Route::get('/api/revenue-chart', [SuperAdminDashboardController::class, 'getRevenueChart'])
            ->name('api.revenue-chart');

        // ---------------------------------------------------------------------
        // GESTION PROPRIÉTAIRES
        // ---------------------------------------------------------------------
        Route::resource('proprios', ProprioManagementController::class)->except(['destroy']);
        
        // Toggle actif/inactif
        Route::post('/proprios/{id}/toggle-active', [ProprioManagementController::class, 'toggleActive'])
            ->name('proprios.toggle-active');
        
        // Impersonation (se connecter en tant que proprio)
        Route::post('/proprios/{id}/impersonate', [ProprioManagementController::class, 'impersonate'])
            ->name('proprios.impersonate')
            ->middleware('superadmin.role:god'); // Seulement GOD
        
        // Arrêter impersonation
        Route::post('/stop-impersonation', [ProprioManagementController::class, 'stopImpersonation'])
            ->name('stop-impersonation');

        // ---------------------------------------------------------------------
        // ANALYTICS GLOBAUX
        // ---------------------------------------------------------------------
        Route::get('/analytics', [GlobalAnalyticsController::class, 'index'])
            ->name('analytics.index');
        
        // API: Performance propriétaires
        Route::get('/api/analytics/proprio-performance', [GlobalAnalyticsController::class, 'getProprioPerformance'])
            ->name('api.analytics.proprio-performance');
        
        // API: Stats par zone
        Route::get('/api/analytics/zone-stats', [GlobalAnalyticsController::class, 'getZoneAnalytics'])
            ->name('api.analytics.zone-stats');
        
        // API: Répartition opérateurs
        Route::get('/api/analytics/operator-breakdown', [GlobalAnalyticsController::class, 'getOperatorBreakdown'])
            ->name('api.analytics.operator-breakdown');
        
        // Export rapports
        Route::get('/analytics/export/{format}', [GlobalAnalyticsController::class, 'exportReport'])
            ->name('analytics.export')
            ->where('format', 'excel|pdf');

        // ---------------------------------------------------------------------
        // VALIDATION RETRAITS
        // ---------------------------------------------------------------------
        Route::get('/withdrawals', [WithdrawalApprovalController::class, 'index'])
            ->name('withdrawals.index');
        
        Route::get('/withdrawals/{id}', [WithdrawalApprovalController::class, 'show'])
            ->name('withdrawals.show');
        
        // Approuver retrait
        Route::post('/withdrawals/{id}/approve', [WithdrawalApprovalController::class, 'approve'])
            ->name('withdrawals.approve')
            ->middleware('superadmin.role:god,admin'); // GOD ou Admin
        
        // Rejeter retrait
        Route::post('/withdrawals/{id}/reject', [WithdrawalApprovalController::class, 'reject'])
            ->name('withdrawals.reject')
            ->middleware('superadmin.role:god,admin');

        // ---------------------------------------------------------------------
        // SUPPORT TICKETS
        // ---------------------------------------------------------------------
        Route::resource('tickets', SupportTicketController::class)->except(['destroy']);
        
        // Assigner ticket
        Route::post('/tickets/{id}/assign', [SupportTicketController::class, 'assign'])
            ->name('tickets.assign');
        
        // Répondre au ticket
        Route::post('/tickets/{id}/reply', [SupportTicketController::class, 'reply'])
            ->name('tickets.reply');
        
        // Changer statut
        Route::post('/tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])
            ->name('tickets.update-status');
        
        // Fermer ticket
        Route::post('/tickets/{id}/close', [SupportTicketController::class, 'close'])
            ->name('tickets.close');

        // ---------------------------------------------------------------------
        // CONFIGURATION SYSTÈME
        // ---------------------------------------------------------------------
        Route::get('/settings', [SystemConfigController::class, 'index'])
            ->name('settings.index')
            ->middleware('superadmin.role:god'); // Seulement GOD
        
        Route::post('/settings', [SystemConfigController::class, 'update'])
            ->name('settings.update')
            ->middleware('superadmin.role:god');

        // ---------------------------------------------------------------------
        // LOGS D'AUDIT
        // ---------------------------------------------------------------------
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');
        
        Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])
            ->name('audit-logs.show');
        
        Route::get('/audit-logs/export/{format}', [AuditLogController::class, 'export'])
            ->name('audit-logs.export')
            ->where('format', 'excel|pdf');

        // ---------------------------------------------------------------------
        // DÉCONNEXION
        // ---------------------------------------------------------------------
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])
            ->name('logout');
    });
});
