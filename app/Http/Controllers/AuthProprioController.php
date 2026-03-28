<?php

namespace App\Http\Controllers;

use App\Models\Proprio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthProprioController extends Controller
{
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_code' => ['required', 'string'],
            'numero' => ['required', 'string', 'max:20', 'unique:proprio,numero'],
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $fullPhone = $data['phone_code'] . ' ' . $data['numero'];

        // Vérifier si le numéro complet existe déjà
        if (Proprio::where('numero', $fullPhone)->exists()) {
            return back()->withErrors(['numero' => 'Ce numéro est déjà utilisé'])->withInput();
        }

        $proprio = Proprio::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'numero' => $fullPhone,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('proprio')->login($proprio);
        return redirect()->route('proprio.dashboard');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone_code' => ['required', 'string'],
            'numero' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $fullPhone = $data['phone_code'] . ' ' . $data['numero'];
        $proprio = Proprio::where('numero', $fullPhone)->first();

        if (!$proprio || !Hash::check($data['password'], $proprio->password)) {
            return back()
                ->withErrors(['login' => 'Numéro ou mot de passe incorrect'])
                ->withInput();
        }

        Auth::guard('proprio')->login($proprio);

        return redirect()->route('proprio.dashboard'); 
    }

    public function logout(Request $request)
    {
        Auth::guard('proprio')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('proprio.login');
    }

    /**
     * Show forgot password initial form (enter phone number)
     */
    public function showForgotPassword(Request $request)
    {
        return view('proprio.login_proprio', [
            'forgot_password_step' => 'enter_phone',
        ]);
    }

    /**
     * Verify phone number and show WhatsApp support button
     */
    public function verifyPhone(Request $request)
    {
        $data = $request->validate([
            'phone_code' => ['required', 'string'],
            'numero' => ['required', 'string'],
        ]);

        $fullPhone = $data['phone_code'] . ' ' . $data['numero'];
        $proprio = Proprio::where('numero', $fullPhone)->first();

        if (!$proprio) {
            return back()->withErrors(['numero' => 'Ce numéro n\'est pas enregistré'])->withInput();
        }

        // Generate WhatsApp message with user info
        $message = "Bonjour, C'est pour une demande réinitialisation de mot de passe.\n\n";
        $message .= "Informations du compte:\n";
        $message .= "- Nom: " . $proprio->nom . "\n";
        $message .= "- Prénom: " . $proprio->prenom . "\n";
        $message .= "- Numéro: " . $proprio->numero;

        $whatsappMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/22967864795?text=" . $whatsappMessage;

        return view('proprio.login_proprio', [
            'forgot_password_step' => 'whatsapp_support',
            'proprio_name' => $proprio->nom,
            'proprio_prenom' => $proprio->prenom,
            'proprio_numero' => $proprio->numero,
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    /**
     * Verify email and send reset link
     */
    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $proprioId = $request->session()->get('forgot_password_proprio_id');
        $expectedEmail = $request->session()->get('forgot_password_email');

        if (!$proprioId || !$expectedEmail) {
            return redirect()->route('proprio.forgot_password')->withErrors(['session' => 'Session expirée']);
        }

        // Verify the email matches exactly
        if (strtolower($data['email']) !== strtolower($expectedEmail)) {
            return back()->withErrors(['email' => 'L\'email ne correspond pas au compte'])->withInput();
        }

        $proprio = Proprio::find($proprioId);

        if (!$proprio) {
            return redirect()->route('proprio.forgot_password')->withErrors(['proprio' => 'Utilisateur non trouvé']);
        }

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = now()->addHours(24);

        // Save token to database
        $proprio->update([
            'password_reset_token' => $token,
            'password_reset_expires_at' => $expiresAt,
        ]);

        // Generate reset link
        $resetLink = url('/reset-password/' . $token);
        
        // Send email with reset link
        try {
            Mail::send('emails.password-reset', [
                'proprio' => $proprio,
                'resetLink' => $resetLink,
            ], function ($message) use ($proprio) {
                $message->to($proprio->email)
                    ->subject('Réinitialisation de votre mot de passe WiFiProfit');
            });
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
        }

        // Clear session
        $request->session()->forget('forgot_password_phone');
        $request->session()->forget('forgot_password_email');
        $request->session()->forget('forgot_password_proprio_id');

        // Generate masked email for display
        $maskedEmail = $this->maskEmail($proprio->email);

        return view('proprio.login_proprio', [
            'forgot_password_step' => 'link_sent',
            'masked_email' => $maskedEmail,
        ]);
    }

    /**
     * Verify email and send reset link
     */
    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find proprio by email
        $proprio = Proprio::where('email', $data['email'])->first();

        if (!$proprio) {
            return back()->withErrors(['email' => 'Aucun compte trouvé avec cette adresse email'])->withInput();
        }

        // Store in session
        $request->session()->put('forgot_password_proprio_id', $proprio->id);
        $request->session()->put('forgot_password_email', $proprio->email);

        // Generate masked email
        $maskedEmail = $this->maskEmail($proprio->email);

        return view('proprio.login_proprio', [
            'forgot_password_step' => 'confirm_email',
            'masked_email' => $maskedEmail,
        ]);
    }

    /**
     * Mask email for display
     */
    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        if (strlen($name) <= 3) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 3) . '***';
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Show reset password form (accessed via WhatsApp link)
     */
    public function showResetForm(Request $request, $token)
    {
        $telephone = $request->query('phone', '');
        $proprio = Proprio::where('password_reset_token', hash('sha256', $token))->first();

        if (!$proprio) {
            return view('proprio.reset-password', [
                'token' => $token,
                'telephone' => $telephone,
                'error' => 'Lien de réinitialisation invalide'
            ]);
        }

        if ($proprio->password_reset_expires_at && now()->greaterThan($proprio->password_reset_expires_at)) {
            return view('proprio.reset-password', [
                'token' => $token,
                'telephone' => $telephone,
                'error' => 'Le lien de réinitialisation a expiré'
            ]);
        }

        return view('proprio.reset-password', [
            'token' => $token,
            'telephone' => $proprio->numero,
            'error' => null
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
            'token' => ['required', 'string'],
            'telephone' => ['required', 'string'],
        ]);

        $proprio = Proprio::where('numero', $data['telephone'])
            ->where('password_reset_token', hash('sha256', $data['token']))
            ->first();

        if (!$proprio) {
            return response()->json([
                'success' => false,
                'message' => 'Lien de réinitialisation invalide'
            ], 422);
        }

        if ($proprio->password_reset_expires_at && now()->greaterThan($proprio->password_reset_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Le lien de réinitialisation a expiré'
            ], 422);
        }

        // Update password and clear token
        $proprio->update([
            'password' => Hash::make($data['password']),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        // Clear session
        $request->session()->forget('forgot_password_phone');
        $request->session()->forget('forgot_password_proprio_id');

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès !',
            'redirect' => url('/login')
        ]);
    }
}
