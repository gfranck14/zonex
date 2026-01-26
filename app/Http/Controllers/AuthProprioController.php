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
            'numero' => ['required', 'digits_between:8,10', 'unique:proprio,numero'],
            'password' => ['required', 'min:6'],
        ]);

        $proprio = Proprio::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'numero' => $data['numero'],
            'password' => Hash::make($data['password']),
        ]);

Auth::guard('proprio')->login($proprio);
        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'numero' => ['required', 'digits_between:8,10'],
            'password' => ['required'],
        ]);

        $proprio = Proprio::where('numero', $credentials['numero'])->first();

        if (!$proprio || !Hash::check($credentials['password'], $proprio->password)) {
            return back()
                ->withErrors(['login' => 'Numéro ou mot de passe incorrect'])
                ->withInput();
        }

        Auth::guard('proprio')->login($proprio); // ✅ utiliser le guard correct

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
