<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthProprioController;
use App\Http\Controllers\WifizoneController;
use App\Http\Controllers\Forfait_ticketController;

// --- ROUTES POUR LES VISITEURS (Non connectés) ---
// Le middleware 'guest:proprio' empêche un utilisateur connecté d'accéder à ces pages
Route::middleware('guest:proprio')->group(function () {
    
    Route::get('/login', function () { 
        return view('proprio.login_proprio');
    })->name('login');

    Route::get('/signup', function () {
        return view('proprio.login_proprio');
    })->name('proprio.signup.form');

    Route::post('/login', [AuthProprioController::class, 'login'])->name('proprio.login');
    Route::post('/signup', [AuthProprioController::class, 'signup'])->name('proprio.signup');
});


// --- ROUTES PROTÉGÉES (Doit être connecté en tant que proprio) ---
Route::middleware('auth:proprio')->group(function () {

    // Route racine qui redirige vers le dashboard
    Route::get('/', function () {  return redirect()->route('dashboard');  });

    // La route dashboard officielle
    Route::get('/dashboard', function () {return view('proprio.index_proprio'); })->name('dashboard');

    Route::get('/wifizones', [WifizoneController::class, 'index'])->name('wifizones');

Route::get('/forfait-ticket', [Forfait_ticketController::class, 'index'])
    ->name('forfait_ticket');

    // Déconnexion
    Route::post('/logout', [AuthProprioController::class, 'logout'])->name('proprio.logout');
    
    Route::get('/wifizones', [WifizoneController::class, 'index'])->name('wifizones');
    Route::post('/wifizones', [WifizoneController::class, 'store'])->name('wifizones.store');
});