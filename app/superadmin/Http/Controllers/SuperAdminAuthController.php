<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\SuperAdmin\Models\SuperAdmin;
use App\SuperAdmin\Models\AuditLog;

/**
 * Controller pour l'authentification des superadmins
 */
class SuperAdminAuthController extends Controller
{
    /**
     * Affiche la page de login
     */
    public function showLogin()
    {
        return view('superadmin.login');
    }

    /**
     * Traite la soumission du formulaire de login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Tentative d'authentification
        if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $superadmin = Auth::guard('superadmin')->user();

            // Vérifier si compte actif
            if (!$superadmin->is_active) {
                Auth::guard('superadmin')->logout();
                return back()->withErrors([
                    'email' => 'Votre compte est désactivé.',
                ]);
            }

            // Mettre à jour dernière connexion
            $superadmin->updateLastLogin($request->ip());

            // Logger la connexion
            AuditLog::logAction(
                user: $superadmin,
                action: 'logged_in',
                model: null,
                oldValues: null,
                newValues: ['ip' => $request->ip()]
            );

            // Si 2FA activé, rediriger vers page 2FA
            if ($superadmin->two_factor_enabled) {
                session(['2fa_user_id' => $superadmin->id]);
                Auth::guard('superadmin')->logout();
                return redirect()->route('superadmin.verify2fa');
            }

            return redirect()->intended(route('superadmin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Affiche et vérifie le code 2FA
     */
    public function verify2FA(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'code' => 'required|string|size:6',
            ]);

            $userId = session('2fa_user_id');
            if (!$userId) {
                return redirect()->route('superadmin.login')
                    ->withErrors(['code' => 'Session expirée. Veuillez vous reconnecter.']);
            }

            $superadmin = SuperAdmin::find($userId);
            if (!$superadmin) {
                return redirect()->route('superadmin.login');
            }

            // Vérifier le code 2FA
            if ($superadmin->verify2FACode($request->code)) {
                session()->forget('2fa_user_id');
                Auth::guard('superadmin')->login($superadmin);
                $request->session()->regenerate();

                $superadmin->updateLastLogin($request->ip());

                return redirect()->route('superadmin.dashboard');
            }

            return back()->withErrors([
                'code' => 'Code incorrect. Veuillez réessayer.',
            ]);
        }

        // GET request - afficher formulaire 2FA
        return view('superadmin.verify-2fa');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();

        // Logger la déconnexion
        if ($superadmin) {
            AuditLog::logAction(
                user: $superadmin,
                action: 'logged_out',
                model: null,
                oldValues: null,
                newValues: ['ip' =>$request->ip()]
            );
        }

        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
