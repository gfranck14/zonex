<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\Wifizone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientPortalController extends Controller
{
    /**
     * Page d'accueil / Landing (Login & Register)
     */
    public function landing(Request $request)
    {
        Log::info('ClientPortalController::landing - Route atteinte');
        Log::info('ClientPortalController::landing - Request URI: ' . $request->getRequestUri());
        Log::info('ClientPortalController::landing - Auth client: ' . (Auth::guard('client')->check() ? 'Connecté' : 'Déconnecté'));
        Log::info('ClientPortalController::landing - Auth proprio: ' . (Auth::guard('proprio')->check() ? 'Connecté' : 'Déconnecté'));
        
        // Détecter la zone via ID (param URL) ou Session
        $zoneId = $request->get('zone_id') ?? session('zone_id');
        
        if (!$zoneId) {
            // Fallback ou erreur si pas de zone identifiée
            // Pour le dev, on peut prendre la première zone
            $wifizone = Wifizone::first();
        } else {
            $wifizone = Wifizone::find($zoneId);
        }

        if ($wifizone) {
            session(['zone_id' => $wifizone->id]);
        }

        // Récupérer MAC address (depuis URL Mikrotik)
        $mac = $request->get('mac_address') ?? $request->get('mac');

        return view('PortailClient.landing', compact('wifizone', 'mac'));
    }

    /**
     * Traitement de l'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'pseudo' => 'required|string|max:255',
            'telephone' => 'required|string|unique:clients,telephone', // Simplifié pour démo
            'password' => 'required|string|min:4|confirmed',
            'mac' => 'nullable|string',
        ]);

        $client = Client::create([
            'pseudo' => $request->pseudo,
            'nom_complet' => $request->pseudo, // Fallback
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'mac_address' => $request->mac,
            'derniere_zone' => session('zone_id'),
            'total_depense' => 0,
        ]);

        // Auto login
        Auth::guard('client')->login($client);

        return redirect()->route('client.shop');
    }

    /**
     * Traitement du login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // MAJ dernière zone
            if (session('zone_id')) {
                $client = Auth::guard('client')->user();
                $client->derniere_zone = session('zone_id');
                $client->save();
            }

            return redirect()->route('client.shop');
        }

        return back()->withErrors([
            'telephone' => 'Identifiants incorrects.',
        ])->onlyInput('telephone');
    }

    /**
     * La Boutique (Shop)
     */
    public function shop()
    {
        $client = Auth::guard('client')->user();
        $zoneId = session('zone_id');
        
        if (!$zoneId) {
            // Rediriger vers landing si pas de zone (ex: session expirée)
            return redirect()->route('client.landing');
        }

        $wifizone = Wifizone::find($zoneId);
        $forfaits = Forfait::where('wifizones_id', $zoneId)->get();

        return view('PortailClient.shop', compact('client', 'wifizone', 'forfaits'));
    }

    /**
     * Achat fictif (Simulation)
     */
    public function buy($forfaitId)
    {
        $client = Auth::guard('client')->user();
        $forfait = Forfait::findOrFail($forfaitId);

        // 1. Chercher un ticket libre existant
        $ticket = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre') // Assumant 'libre' comme statut dispo
            ->first();

        // 2. Si pas de ticket, on en génère un à la volée (Mock)
        if (!$ticket) {
            $ticket = Ticket::create([
                'forfaits_id' => $forfait->id,
                'username' => Str::upper(Str::random(6)), // Ex: K8JS2A
                'password' => rand(1000, 9999),          // Ex: 4582
                'statut' => 'libre',
                'date_vente' => null,
            ]);
        }

        // 3. "Vente" du ticket
        $ticket->update([
            'statut' => 'vendu',
            'client_id' => $client->id,
            'date_vente' => now(),
        ]);

        // MAJ Dépense Client
        $client->increment('total_depense', $forfait->prix);

        // 4. Redirection vers la livraison
        return redirect()->route('client.ticket', ['ticket' => $ticket->id]);
    }

    /**
     * Livraison du Ticket
     */
    public function ticket(Ticket $ticket)
    {
        // Vérification sécurité : le ticket appartient bien au client connecté
        if ($ticket->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        $wifizone = $ticket->forfait->wifizone;
        // Adresse IP Mikrotik typique ou depuis la zone
        // TODO: Ajouter champ mikrotik_ip dans wifizone table si nécessaire
        // Pour l'instant on mock ou on prend une valeur par défaut
        $mikrotik_ip = '10.5.50.1'; // IP par défaut Hotspot Mikrotik

        return view('PortailClient.ticket', compact('ticket', 'wifizone', 'mikrotik_ip'));
    }
    
    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('client.landing');
    }
}
