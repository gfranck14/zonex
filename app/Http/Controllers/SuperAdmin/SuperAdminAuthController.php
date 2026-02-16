<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SuperAdminAuthController extends Controller
{
    /**
     * Affiche le formulaire de login
     */
    public function showLogin()
    {
        if (Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.dashboard');
        }
        return view('superadmin.super_admin_login');
    }

    /**
     * Traite la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Tentative de connexion
        if (Auth::guard('superadmin')->attempt($credentials)) {
            // Vérifier si le 2FA est activé
            $superAdmin = Auth::guard('superadmin')->user();
            
            if ($superAdmin->two_factor_enabled) {
                // Stocker l'ID en session pour la vérification 2FA
                Session::put('2fa_pending_superadmin', $superAdmin->id);
                Session::put('2fa_type', $superAdmin->two_factor_type);
                
                // Déconnecter temporairement
                Auth::guard('superadmin')->logout();
                
                // Rediriger vers la page de vérification 2FA
                return redirect()->route('superadmin.verify2fa')
                    ->with('info', 'Code 2FA requis');
            }

            // Connexion réussie sans 2FA
            Session::regenerate();
            
            Log::info('SuperAdmin login successful', ['email' => $request->email]);
            
            return redirect()->intended(route('superadmin.dashboard'))
                ->with('success', 'Connexion réussie');
        }

        Log::warning('SuperAdmin login failed', ['email' => $request->email]);
        
        return back()->withErrors([
            'email' => 'Les identifiants sont incorrects.',
        ])->onlyInput('email');
    }

    /**
     * Vérifie le code 2FA
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $superAdminId = Session::get('2fa_pending_superadmin');
        
        if (!$superAdminId) {
            return redirect()->route('superadmin.login')
                ->with('error', 'Session expirée');
        }

        $superAdmin = SuperAdmin::find($superAdminId);
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login')
                ->with('error', 'Utilisateur non trouvé');
        }

        // Vérifier le code
        $isValid = false;
        
        if ($superAdmin->two_factor_type === 'totp') {
            $isValid = $superAdmin->verifyTotpCode($request->code);
        } elseif ($superAdmin->two_factor_type === 'email') {
            $isValid = $superAdmin->verifyEmailCode($request->code);
        }

        if (!$isValid) {
            return back()->with('error', 'Code invalide');
        }

        // Connecter l'utilisateur
        Auth::guard('superadmin')->login($superAdmin);
        Session::forget(['2fa_pending_superadmin', '2fa_type']);
        Session::regenerate();

        Log::info('SuperAdmin 2FA login successful', ['id' => $superAdmin->id]);

        return redirect()->intended(route('superadmin.dashboard'))
            ->with('success', 'Connexion réussie');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('superadmin.login')
            ->with('success', 'Déconnexion réussie');
    }
}
