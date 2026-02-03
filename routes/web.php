<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthProprioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WifizoneController;
use App\Http\Controllers\ForfaitController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;


// =============================================================================
// 3. ROUTES DU PORTAIL CLIENT (Front-Office)
// =============================================================================
// Routes pour le parcours client: Landing, Auth, Shop, Ticket

// Route racine : redirige vers le portail client pour la démo
Route::get('/', function () {
    return redirect()->route('client.landing');
});

// Groupe Portail Client
Route::prefix('portal')->name('client.')->group(function () {
    // Publiques
    Route::get('/landing', [ClientPortalController::class, 'landing'])->name('landing');
    Route::post('/register', [ClientPortalController::class, 'register'])->name('register');
    Route::post('/login', [ClientPortalController::class, 'login'])->name('login');
    
    // Protégées (Besoin d'être connecté en tant que 'client')
    Route::middleware('auth:client')->group(function () {
        Route::get('/shop', [ClientPortalController::class, 'shop'])->name('shop');
        Route::post('/buy/{forfait}', [ClientPortalController::class, 'buy'])->name('buy');
        Route::get('/ticket/{ticket}', [ClientPortalController::class, 'ticket'])->name('ticket');
        Route::post('/logout', [ClientPortalController::class, 'logout'])->name('logout');
    });
});

// =============================================================================
// Application de gestion de zones WiFi et de ventes de tickets
// Développée pour les propriétaires de zones WiFi (bars, restaurants, hôtels, etc.)

// =============================================================================
// 1. ROUTES POUR LES VISITEURS (Non connectés)
// =============================================================================
// Ces routes sont accessibles à tous les utilisateurs non connectés en tant que propriétaire
// Le middleware 'guest:proprio' empêche un utilisateur connecté d'accéder à ces pages
Route::middleware('guest:proprio')->group(function () {
    
    /**
     * Affiche la page de login pour les propriétaires
     * Nom de route: login
     */
    Route::get('/login', function () { 
        return view('proprio.login_proprio');
    })->name('login');

    /**
     * Affiche la page d'inscription pour les propriétaires
     * Nom de route: proprio.signup.form
     */
    Route::get('/signup', function () {
        return view('proprio.login_proprio');
    })->name('proprio.signup.form');

    /**
     * Traite la soumission du formulaire de login
     * Nom de route: proprio.login
     */
    Route::post('/login', [AuthProprioController::class, 'login'])->name('proprio.login');
    
    /**
     * Traite la soumission du formulaire d'inscription
     * Nom de route: proprio.signup
     */
    Route::post('/signup', [AuthProprioController::class, 'signup'])->name('proprio.signup');
});


// =============================================================================
// 2. ROUTES PROTÉGÉES (Connecté en tant que propriétaire)
// =============================================================================
// Ces routes nécessitent une authentification valide en tant que propriétaire
// Le middleware 'auth:proprio' vérifie que l'utilisateur est connecté
Route::middleware('auth:proprio')->group(function () {

    /**
     * Route racine qui redirige vers le dashboard
     * Utile pour les accès directs à la racine du site
     */
    // Route racine redirigée globalement plus bas vers le portail client
    // Route::get('/', ...); 

    // Routes Portail Client
    Route::prefix('portal')->name('client.')->group(function () {
        Route::get('/landing', [ClientPortalController::class, 'landing'])->name('landing');
        Route::post('/register', [ClientPortalController::class, 'register'])->name('register');
        Route::post('/login', [ClientPortalController::class, 'login'])->name('login');
        
        // Routes protégées client
        Route::middleware('auth:client')->group(function () {
            Route::get('/shop', [ClientPortalController::class, 'shop'])->name('shop');
            Route::post('/buy/{forfait}', [ClientPortalController::class, 'buy'])->name('buy');
            Route::get('/ticket/{ticket}', [ClientPortalController::class, 'ticket'])->name('ticket');
            Route::post('/logout', [ClientPortalController::class, 'logout'])->name('logout');
        });
    });

    // -------------------------------------------------------------------------
    // ROUTES DU DASHBOARD
    // -------------------------------------------------------------------------
    
    /**
     * Affiche le dashboard principal du propriétaire
     * Contient les KPIs (tickets vendus, revenu du jour, stock disponible, etc.)
     * et les graphiques de ventes des 7 derniers jours
     * Nom de route: dashboard
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // -------------------------------------------------------------------------
    // ROUTES DES ZONES WIFI
    // -------------------------------------------------------------------------
    
    /**
     * Affiche la page de gestion des zones WiFi
     * Permet de voir, ajouter, modifier et supprimer des zones
     * Nom de route: wifizones
     */
    Route::get('/wifizones', [WifizoneController::class, 'index'])->name('wifizones');
    
    /**
     * Traite la création d'une nouvelle zone WiFi
     * Nom de route: wifizones.store
     */
    Route::post('/wifizones', [WifizoneController::class, 'store'])->name('wifizones.store');
    
    /**
     * Récupère les données d'impact d'une zone WiFi
     * Utilisé pour afficher les statistiques détaillées d'une zone
     * Nom de route: wifizones.impact
     */
    Route::get('/wifizones/{id}/impact', [WifizoneController::class, 'getImpact'])->name('wifizones.impact');
    
    /**
     * Traite la mise à jour d'une zone WiFi existante
     * Nom de route: wifizones.update
     */
    Route::put('/wifizones/{id}', [WifizoneController::class, 'update'])->name('wifizones.update');
    
    /**
     * Traite la suppression d'une zone WiFi
     * Nom de route: wifizones.destroy
     */
    Route::delete('/wifizones/{id}', [WifizoneController::class, 'destroy'])->name('wifizones.destroy');

    // -------------------------------------------------------------------------
    // ROUTES DES FORFAITS ET TICKETS
    // -------------------------------------------------------------------------
    
    /**
     * Affiche la page de gestion des forfaits et tickets
     * Contient les onglets: Stock (ajout/modification des forfaits),
     * Liste (gestion des tickets), Importation CSV et Historique
     * Nom de route: forfait_ticket
     */
    Route::get('/forfait-ticket', [ForfaitController::class, 'index'])->name('forfait_ticket');
    
    /**
     * Alias pour la route /forfait-ticket
     * Nom de route: tickets.index
     */
    Route::get('/tickets', [ForfaitController::class, 'index'])->name('tickets.index');
    
    /**
     * Traite la création d'un nouveau forfait
     * Nom de route: forfaits.store
     */
    Route::post('/forfaits', [ForfaitController::class, 'store'])->name('forfaits.store');
    
    /**
     * Traite la mise à jour d'un forfait existant
     * Nom de route: forfaits.update
     */
    Route::put('/forfaits/{id}', [ForfaitController::class, 'update'])->name('forfaits.update');
    
    /**
     * Traite la suppression d'un forfait
     * Nom de route: forfaits.destroy
     */
    Route::delete('/forfaits/{id}', [ForfaitController::class, 'destroy'])->name('forfaits.destroy');
    
    /**
     * Récupère les détails d'un forfait pour modification
     * Retourne les données au format JSON
     * Nom de route: forfaits.edit
     */
    Route::get('/forfaits/{id}/edit', [ForfaitController::class, 'edit'])->name('forfaits.edit');
    
    /**
     * Traite l'importation de tickets depuis un fichier CSV
     * Nom de route: tickets.import
     */
    Route::post('/tickets/import', [ForfaitController::class, 'import'])->name('tickets.import');
    
    /**
     * Traite la suppression d'un ticket
     * Nom de route: tickets.destroy
     */
    Route::delete('/tickets/{id}', [ForfaitController::class, 'destroyTicket'])->name('tickets.destroy');

    // -------------------------------------------------------------------------
    // ROUTES DE SUPPRESSION EN MASSE DE TICKETS
    // -------------------------------------------------------------------------

    /**
     * Prévisualise les tickets à supprimer avant confirmation
     * Retourne le nombre de tickets libres et vendus
     * Nom de route: tickets.preview
     */
    Route::get('/tickets/preview', [TicketController::class, 'previewDelete'])->name('tickets.preview');

    /**
     * Supprime tous les tickets d'un forfait spécifique
     * Nom de route: tickets.delete.forfait
     */
    Route::delete('/tickets/by-forfait/{forfait}', [TicketController::class, 'deleteByForfait'])->name('tickets.delete.forfait');

    /**
     * Supprime tous les tickets d'une zone WiFi (tous forfaits)
     * Nom de route: tickets.delete.zone
     */
    Route::delete('/tickets/by-zone/{zone}', [TicketController::class, 'deleteByZone'])->name('tickets.delete.zone');

    /**
     * Supprime tous les tickets créés à une date spécifique
     * Nom de route: tickets.delete.date
     */
    Route::delete('/tickets/by-date', [TicketController::class, 'deleteByDate'])->name('tickets.delete.date');

    /**
     * Supprime tous les tickets d'un batch d'import spécifique
     * Nom de route: tickets.delete.batch
     */
    Route::delete('/tickets/by-batch/{batch}', [TicketController::class, 'deleteByBatch'])->name('tickets.delete.batch');

    // -------------------------------------------------------------------------
    // ROUTES DES CLIENTS
    // -------------------------------------------------------------------------
    
    /**
     * Affiche la page de gestion des clients
     * Permet de voir, ajouter, modifier et bloquer/débloquer des clients
     * Nom de route: clients
     */
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    
    /**
     * Traite la création d'un nouveau client
     * Nom de route: clients.store
     */
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    
    /**
     * Traite la mise à jour d'un client existant
     * Retourne les données au format JSON
     * Nom de route: clients.update
     */
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    
    /**
     * Traite la suppression d'un client
     * Retourne les données au format JSON
     * Nom de route: clients.destroy
     */
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    
    /**
     * Bascule le statut de blocage d'un client
     * Retourne les données au format JSON
     * Nom de route: clients.block
     */
    Route::post('/clients/{id}/block', [ClientController::class, 'toggleBlock'])->name('clients.block');
    
    /**
     * Récupère l'historique des tickets d'un client
     * Retourne les données au format JSON
     * Nom de route: clients.history
     */
    Route::get('/clients/{id}/history', [ClientController::class, 'history'])->name('clients.history');
    
    // -------------------------------------------------------------------------
    // ROUTES DES PAIEMENTS ET RETRAITS
    // -------------------------------------------------------------------------
    
    /**
     * Affiche la page de gestion des paiements et retraits
     * Permet de voir les transactions, solde par opérateur et historique des retraits
     * Nom de route: paiements
     */
    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements');
    
    /**
     * API endpoint: Récupère les données de solde par opérateur
     * Retourne les données au format JSON
     * Nom de route: api.balance
     */
    Route::get('/api/balance', [PaiementController::class, 'getBalance'])->name('api.balance');
    
    /**
     * API endpoint: Récupère les transactions filtrées
     * Retourne les données au format JSON
     * Nom de route: api.transactions
     */
    Route::get('/api/transactions', [PaiementController::class, 'getTransactions'])->name('api.transactions');
    
    /**
     * API endpoint: Traite la demande de retrait de fonds
     * Nom de route: api.withdrawals.store
     */
    Route::post('/api/withdrawals', [WithdrawalController::class, 'store'])->name('api.withdrawals.store');
    
    /**
     * API endpoint: Récupère la liste des demandes de retrait
     * Nom de route: api.withdrawals.index
     */
    Route::get('/api/withdrawals', [WithdrawalController::class, 'index'])->name('api.withdrawals.index');
    
    /**
     * API endpoint: Traite l'annulation d'une demande de retrait
     * Nom de route: api.withdrawals.cancel
     */
    Route::post('/api/withdrawals/{id}/cancel', [WithdrawalController::class, 'cancel'])->name('api.withdrawals.cancel');
    
    // -------------------------------------------------------------------------
    // ROUTES DES PARAMÈTRES
    // -------------------------------------------------------------------------
    
    /**
     * Affiche la page des paramètres du compte propriétaire
     * Contient les sections: Profil, Mot de passe, WhatsApp et Compte
     * Nom de route: settings
     */
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    
    /**
     * Traite la mise à jour des informations du profil
     * Nom de route: settings.profile.update
     */
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    
    /**
     * Traite la mise à jour du mot de passe
     * Nom de route: settings.password.update
     */
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    
    /**
     * Traite la mise à jour des paramètres WhatsApp
     * Nom de route: settings.whatsapp.update
     */
    Route::post('/settings/whatsapp', [SettingsController::class, 'updateWhatsApp'])->name('settings.whatsapp.update');
    
    /**
     * Traite la désactivation du compte
     * Nom de route: settings.deactivate
     */
    Route::post('/settings/deactivate', [SettingsController::class, 'deactivate'])->name('settings.deactivate');
    
    /**
     * Traite la réactivation du compte
     * Nom de route: settings.activate
     */
    Route::post('/settings/activate', [SettingsController::class, 'activate'])->name('settings.activate');
    
    /**
     * Traite la suppression définitive du compte
     * Nom de route: settings.delete
     */
    Route::delete('/settings/delete', [SettingsController::class, 'deleteAccount'])->name('settings.delete');

    // -------------------------------------------------------------------------
    // ROUTE DE DÉCONNEXION
    // -------------------------------------------------------------------------
    
    /**
     * Traite la déconnexion de l'utilisateur
     * Nom de route: proprio.logout
     */
    Route::post('/logout', [AuthProprioController::class, 'logout'])->name('proprio.logout');
});
