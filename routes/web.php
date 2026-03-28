<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthProprioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WifizoneController;
use App\Http\Controllers\ForfaitController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\FedapayController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MikrotikEventController;



use App\Http\Controllers\HotspotController;



// SuperAdmin Controllers
use App\Http\Controllers\SuperAdmin\SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\ProprioManagementController;
use App\Http\Controllers\SuperAdmin\GlobalAnalyticsController;
use App\Http\Controllers\SuperAdmin\WithdrawalApprovalController;
use App\Http\Controllers\SuperAdmin\SupportTicketController;


// =============================================================================
// 3. ROUTES DU PORTAIL CLIENT (Front-Office)
// =============================================================================
// Routes pour le parcours client: Landing, Auth, Shop, Ticket

// =============================================================================
// WEBHOOK MIKROTIK HOTSPOT (PUBLIQUE - sans CSRF)
// =============================================================================
Route::prefix('api/hotspot')->name('mikrotik.')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    // Webhook unique pour logout avec zone
    Route::post('/logout', [MikrotikEventController::class, 'handleLogoutWithZone'])
         ->name('logout');
});


Route::post('/hotspot/logout', [HotspotController::class, 'logout']);



// Route racine : redirige vers le portail client pour la démo
Route::get('/', function () {
    return redirect()->route('proprio.dashboard');
});

// Page d'accueil centre de soins (démonstration)
Route::get('/care-center-home', function () {
    return view('care-center-home');
})->name('care.center.home');

// Dashboard centre de soins
Route::get('/care-center-dashboard', function () {
    return view('care-center-dashboard');
})->name('care.center.dashboard');

// Groupe Portail Client
Route::prefix('portal')->name('client.')->group(function () {
    // Page d'accueil du portail (redirection par défaut)
    Route::get('/', function () {
        return view('PortailClient.landing-default')->with('info', 'Veuillez entrer un token pour accéder au portail.');
    })->name('landing.default');
    
    // Publiques
    Route::get('/landing/{token}', [ClientPortalController::class, 'landing'])->name('landing');
    Route::post('/register', [ClientPortalController::class, 'register'])->name('register');
    Route::post('/login/{token}', [ClientPortalController::class, 'login'])->name('login');
    Route::get('/get-hotspot-address/{token}', [ClientPortalController::class, 'getHotspotAddress'])->name('get-hotspot-address');
    Route::post('/verify-admin-credentials', [ClientPortalController::class, 'verifyAdminCredentials'])->name('verify-admin-credentials');
    
    // Réinitialisation de mot de passe client
    Route::get('/reset-password/{token}', [ClientPortalController::class, 'showResetPasswordForm'])->name('reset-password.form');
    Route::post('/reset-password', [ClientPortalController::class, 'resetPassword'])->name('reset-password.submit');
    Route::get('/reset-password-success', [ClientPortalController::class, 'showResetPasswordSuccessPage'])->name('reset-password.success.page');
    Route::get('/password-reset-success/{token}', [ClientPortalController::class, 'showPasswordResetSuccess'])->name('password.reset.success');
    
    // Route Fedapay publique pour paiement direct
    Route::post('/fedapay/pay-direct/{forfait}', [FedapayController::class, 'payDirect'])->name('fedapay.pay.direct');
    
    // Route Fedapay publique avec vérification manuelle
    Route::post('/fedapay/pay/{forfait}', [FedapayController::class, 'initiatePayment'])->name('fedapay.initiate');
    
    // Protégées (Besoin d'être connecté en tant que 'client')
    Route::middleware(['auth:client', 'redirect.if.not.client'])->group(function () {
        Route::get('/shop', [ClientPortalController::class, 'shop'])->name('shop');
        Route::post('/buy/{forfait}', [ClientPortalController::class, 'buy'])->name('buy');
        Route::get('/ticket/{ticket}', [ClientPortalController::class, 'ticket'])->name('ticket');
        Route::post('/logout', [ClientPortalController::class, 'logout'])->name('logout');
        
        // Routes Fedapay protégées
        Route::get('/payment/{forfait}', [FedapayController::class, 'showPaymentForm'])->name('fedapay.form');
        Route::get('/payment-no-phone/{forfait}', [FedapayController::class, 'showCheckoutWithoutPhone'])->name('fedapay.checkout.no-phone');
        Route::get('/checkout/{forfait}', [FedapayController::class, 'showCheckoutWithoutPhone'])->name('fedapay.checkout');
    });
    
    // Routes publiques Fedapay (callbacks et webhooks)
    Route::prefix('fedapay')->name('fedapay.')->group(function () {
        Route::get('/callback', [FedapayController::class, 'paymentCallback'])->name('callback');
        Route::post('/webhook', [FedapayController::class, 'webhook'])->name('webhook');
        Route::get('/payment-success', [FedapayController::class, 'paymentSuccess'])->name('payment.success');
    });
});

// =============================================================================
// Application de gestion de zones WiFi et de ventes de tickets
// =============================================================================

// =============================================================================
// 1. ROUTES POUR LES VISITEURS (Non connectés)
// =============================================================================
Route::middleware('guest:proprio')->name('proprio.')->group(function () {
    Route::get('/login', function () { 
        return view('proprio.login_proprio');
    })->name('login');

    Route::get('/signup', function () {
        return view('proprio.login_proprio');
    })->name('signup.form');

    Route::post('/login', [AuthProprioController::class, 'login'])->name('login.submit');
    Route::post('/signup', [AuthProprioController::class, 'signup'])->name('signup');
    
    // Routes Mot de passe oublié
    Route::get('/forgot-password', [AuthProprioController::class, 'showForgotPassword'])->name('forgot_password');
    Route::post('/forgot-password/verify-phone', [AuthProprioController::class, 'verifyPhone'])->name('forgot_password.verify_phone');
    Route::post('/forgot-password/verify-email', [AuthProprioController::class, 'verifyEmail'])->name('forgot_password.verify_email');
    Route::post('/forgot-password/send-link', [AuthProprioController::class, 'sendResetLink'])->name('forgot_password.send_link');
});

// Routes publiques pour réinitialisation de mot de passe (accessible même si connecté)
Route::get('/reset-password/{token}', [AuthProprioController::class, 'showResetForm'])->name('proprio.forgot_password.reset_form');
Route::post('/reset-password', [AuthProprioController::class, 'resetPassword'])->name('proprio.forgot_password.reset');


// =============================================================================
// 2. ROUTES PROTÉGÉES (Connecté en tant que propriétaire)
// =============================================================================
Route::middleware('auth:proprio')->name('proprio.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Zones WiFi
    Route::get('/wifizones', [WifizoneController::class, 'index'])->name('wifizones');
    Route::post('/wifizones', [WifizoneController::class, 'store'])->name('wifizones.store');
    Route::get('/wifizones/{id}/impact', [WifizoneController::class, 'getImpact'])->name('wifizones.impact');
    Route::put('/wifizones/{id}', [WifizoneController::class, 'update'])->name('wifizones.update');
    Route::delete('/wifizones/{id}', [WifizoneController::class, 'destroy'])->name('wifizones.destroy');
    Route::post('/wifizones/test-connection', [WifizoneController::class, 'testConnection'])->name('wifizones.test-connection');
    Route::post('/wifizones/{id}/ticket-credentials', [WifizoneController::class, 'saveTicketCredentials'])->name('wifizones.ticket-credentials');

    // Forfaits et Tickets
    Route::get('/forfait-ticket', [ForfaitController::class, 'index'])->name('forfait_ticket');
    Route::get('/tickets', [ForfaitController::class, 'index'])->name('tickets.index');
    Route::post('/forfaits', [ForfaitController::class, 'store'])->name('forfaits.store');
    Route::get('/forfaits/{id}/edit', [ForfaitController::class, 'edit'])->name('forfaits.edit');
    Route::get('/forfaits/{id}/check-active', [ForfaitController::class, 'checkActiveSessions'])->name('forfaits.check-active');
    Route::put('/forfaits/{id}', [ForfaitController::class, 'update'])->name('forfaits.update');
    Route::post('/forfaits/{id}/toggle-active', [ForfaitController::class, 'toggleActive'])->name('forfaits.toggle-active');
    Route::post('/forfaits/sync-profiles/{zoneId}', [ForfaitController::class, 'importMikrotikProfiles'])->name('forfaits.sync-profiles');
    Route::post('/forfaits/sync-tickets/{zoneId}', [ForfaitController::class, 'syncMikrotikTickets'])->name('forfaits.sync-tickets');
    Route::delete('/forfaits/{id}', [ForfaitController::class, 'destroy'])->name('forfaits.destroy');
    Route::get('/forfaits/{id}/edit', [ForfaitController::class, 'edit'])->name('forfaits.edit');
    Route::post('/forfaits/set-active-zone', [ForfaitController::class, 'setActiveZone'])->name('forfaits.set-active-zone');
    Route::post('/tickets/import', [ForfaitController::class, 'import'])->name('tickets.import');
    Route::delete('/tickets/{id}', [ForfaitController::class, 'destroyTicket'])->name('tickets.destroy');
    Route::post('/generate-tickets', [ForfaitController::class, 'generateTickets'])->name('generate.tickets');

    // Suppression tickets
    Route::get('/tickets/preview', [TicketController::class, 'previewDelete'])->name('tickets.preview');
    Route::delete('/tickets/by-forfait/{forfait}', [TicketController::class, 'deleteByForfait'])->name('tickets.delete.forfait');
    Route::delete('/tickets/by-zone/{zone}', [TicketController::class, 'deleteByZone'])->name('tickets.delete.zone');
    Route::delete('/tickets/by-date', [TicketController::class, 'deleteByDate'])->name('tickets.delete.date');
    Route::delete('/tickets/by-batch/{batch}', [TicketController::class, 'deleteByBatch'])->name('tickets.delete.batch');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{id}/block', [ClientController::class, 'toggleBlock'])->name('clients.block');
    Route::post('/clients/{id}/reset-password', [ClientController::class, 'resetPassword'])->name('clients.reset-password');
    Route::post('/clients/{id}/reset-password-link', [ClientController::class, 'generateResetPasswordLink'])->name('clients.reset-password-link');
    Route::get('/clients/{id}/history', [ClientController::class, 'history'])->name('clients.history');
    
    // Paiements et Retraits (Wizard & API)
    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements');
    Route::get('/api/balance', [PaiementController::class, 'getBalance'])->name('api.balance');
    Route::get('/api/transactions', [PaiementController::class, 'getTransactions'])->name('api.transactions');
    
    // API Retraits (Consolidée)
    Route::prefix('api/withdrawals')->name('api.withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::post('/', [WithdrawalController::class, 'store'])->name('store');
        Route::post('/{id}/cancel', [WithdrawalController::class, 'cancel'])->name('cancel');
    });
    
    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/whatsapp', [SettingsController::class, 'updateWhatsApp'])->name('settings.whatsapp.update');
    Route::post('/settings/deactivate', [SettingsController::class, 'deactivate'])->name('settings.deactivate');
    Route::post('/settings/activate', [SettingsController::class, 'activate'])->name('settings.activate');
    Route::delete('/settings/delete', [SettingsController::class, 'deleteAccount'])->name('settings.delete');

    // Déconnexion
    Route::post('/logout', [AuthProprioController::class, 'logout'])->name('logout');
 });


// =============================================================================
// 4. ROUTES SUPER ADMIN (/god-admin/)
// =============================================================================
Route::prefix('god-admin')->name('superadmin.')->group(function () {
    
    // Page d'accueil - redirige vers login ou dashboard
    Route::get('/', function () {
        if (Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('superadmin.login');
    });
    
    // Authentification
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.submit');
        Route::post('/verify-2fa', [SuperAdminAuthController::class, 'verify2FA'])->name('verify2fa');
    });

    // Routes protégées
    Route::middleware(['superadmin.auth'])->group(function () {
        
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/realtime-stats', [SuperAdminDashboardController::class, 'getRealtimeStats'])->name('api.realtime-stats');
        Route::get('/api/revenue-chart', [SuperAdminDashboardController::class, 'getRevenueChart'])->name('api.revenue-chart');

        // Gestion propriétaires
        Route::get('/proprietaires', [SuperAdminController::class, 'proprietaires'])->name('proprietaires');
        
        // Générer un lien de réinitialisation de mot de passe pour un propriétaire
        Route::post('/proprio/reset-password-link', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'proprio_id' => 'required|exists:proprio,id'
            ]);
            
            $proprio = \App\Models\Proprio::findOrFail($request->proprio_id);
            
            // Générer un token unique
            $token = \Illuminate\Support\Str::random(64);
            
            // Stocker le token dans la table proprio (comme le système existant)
            $proprio->update([
                'password_reset_token' => hash('sha256', $token),
                'password_reset_expires_at' => now()->addHours(24)
            ]);
            
            // Générer l'URL de réinitialisation avec le numéro de téléphone
            $resetUrl = url('/reset-password/' . $token . '?phone=' . urlencode($proprio->numero));
            
            return response()->json([
                'success' => true,
                'message' => 'Lien de réinitialisation généré avec succès',
                'reset_url' => $resetUrl
            ]);
        })->name('proprio.reset-password-link');
        
        // Gestion clients
        Route::get('/clients', [SuperAdminController::class, 'clients'])->name('clients');
        Route::get('/clients/{id}/tickets', [SuperAdminController::class, 'clientTickets'])->name('clients.tickets');
        
        // Gestion retraits
        Route::get('/retraits', [SuperAdminController::class, 'retraits'])->name('retraits');

        // Gestion propriétaires existante
        Route::resource('proprios', ProprioManagementController::class)->except(['destroy']);
        Route::post('/proprios/{id}/toggle-active', [ProprioManagementController::class, 'toggleActive'])->name('proprios.toggle-active');
        Route::post('/proprios/{id}/impersonate', [ProprioManagementController::class, 'impersonate'])->name('proprios.impersonate')->middleware('superadmin.role:god');
        Route::post('/stop-impersonation', [ProprioManagementController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Analytics
        Route::get('/analytics', [GlobalAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/api/analytics/proprio-performance', [GlobalAnalyticsController::class, 'getProprioPerformance'])->name('analytics.proprio-performance');
        Route::get('/api/analytics/zone-stats', [GlobalAnalyticsController::class, 'getZoneAnalytics'])->name('analytics.zone-stats');
        Route::get('/api/analytics/operator-breakdown', [GlobalAnalyticsController::class, 'getOperatorBreakdown'])->name('analytics.operator-breakdown');
        Route::get('/analytics/export/{format}', [GlobalAnalyticsController::class, 'exportReport'])->name('analytics.export')->where('format', 'excel|pdf');

        // Retraits
        Route::get('/withdrawals', [WithdrawalApprovalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/{id}', [WithdrawalApprovalController::class, 'show'])->name('withdrawals.show');
        Route::post('/withdrawals/{id}/pay', [WithdrawalApprovalController::class, 'pay'])->name('withdrawals.pay')->middleware('superadmin.role:god,admin');
        Route::post('/withdrawals/{id}/reject', [WithdrawalApprovalController::class, 'reject'])->name('withdrawals.reject')->middleware('superadmin.role:god,admin');

        // Support Tickets
        Route::resource('tickets', SupportTicketController::class)->except(['destroy']);
        Route::post('/tickets/{id}/assign', [SupportTicketController::class, 'assign'])->name('tickets.assign');
        Route::post('/tickets/{id}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('/tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.update-status');
        Route::post('/tickets/{id}/close', [SupportTicketController::class, 'close'])->name('tickets.close');

        // Déconnexion
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
    });
});


// =============================================================================
// 5. ROUTES SUPER ADMIN (/super_admin/)
// =============================================================================
Route::prefix('super_admin')->name('super_admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('superadmin.super_admin_dashboard');
    })->name('dashboard');
    
    Route::get('/clients', function () {
        $clients = \App\Models\Client::paginate(10);
        $totalClients = \App\Models\Client::count();
        $nouveauxClients = \App\Models\Client::where('created_at', '>=', now()->subDays(30))->count();
        return view('superadmin.super_admin_clients', compact('clients', 'totalClients', 'nouveauxClients'));
    })->name('clients');
    
    Route::get('/proprios', function () {
        $proprios = \App\Models\Proprio::paginate(10);
        $totalProprios = \App\Models\Proprio::count();
        $actifs = \App\Models\Proprio::where('is_active', true)->count();
        $inactifs = \App\Models\Proprio::where('is_active', false)->count();
        return view('superadmin.super_admin_proprios', compact('proprios', 'totalProprios', 'actifs', 'inactifs'));
    })->name('proprios');
    
    Route::get('/proprios/{id}', function ($id) {
        $proprio = \App\Models\Proprio::findOrFail($id);
        return view('superadmin.super_admin_proprios', compact('proprio'));
    })->name('proprios.show');
    
    Route::post('/proprios/{id}/toggle', function ($id) {
        $proprio = \App\Models\Proprio::findOrFail($id);
        $proprio->is_active = !$proprio->is_active;
        $proprio->save();
        return back()->with('success', 'Statut mis à jour');
    })->name('proprios.toggle');
    
    Route::get('/retraits', function () {
        $retraits = \App\Models\Retrait::with('user')->paginate(10);
        $totalRetraits = \App\Models\Retrait::count();
        $retraitsEnAttente = \App\Models\Retrait::where('statut', 'en_attente')->count();
        $retraitsApprouves = \App\Models\Retrait::where('statut', 'paye')->count();
        $montantTotal = \App\Models\Retrait::where('statut', 'paye')->sum('montant');
        return view('superadmin.super_admin_retraits', compact('retraits', 'totalRetraits', 'retraitsEnAttente', 'retraitsApprouves', 'montantTotal'));
    })->name('retraits');
    
    Route::get('/retraits/{id}', function ($id) {
        $retrait = \App\Models\Retrait::with('user')->findOrFail($id);
        return view('superadmin.super_admin_retraits', compact('retrait'));
    })->name('retraits.show');
    
    Route::post('/retraits/{id}/approve', function ($id) {
        $retrait = \App\Models\Retrait::findOrFail($id);
        $retrait->statut = 'approuve';
        $retrait->save();
        return back()->with('success', 'Retrait approuvé');
    })->name('retraits.approve');
    
    Route::post('/retraits/{id}/reject', function ($id) {
        $retrait = \App\Models\Retrait::findOrFail($id);
        $retrait->statut = 'rejete';
        $retrait->save();
        return back()->with('success', 'Retrait rejeté');
    })->name('retraits.reject');
    
    Route::get('/tickets', function () {
        return view('superadmin.super_admin_tickets');
    })->name('tickets');
    
    Route::get('/settings', function () {
        return view('superadmin.super_admin_settings');
    })->name('settings');
});
