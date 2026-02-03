<?php

namespace App\Http\Controllers;

use App\Models\Proprio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password' => ['required', 'min:6', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
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
        return redirect()->route('dashboard');
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

        return redirect()->route('dashboard'); 
    }

    public function logout(Request $request)
    {
        Auth::guard('proprio')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
