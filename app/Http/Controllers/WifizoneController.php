<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WifiZone;   
use Illuminate\Support\Str;  

class WifizoneController extends Controller
{
    /**
     * Affiche la page Wifi Zones
     */
    public function index()
    {
         // 1. Récupérer l'ID du proprio connecté
        $proprioId = Auth::guard('proprio')->id();

        // 2. Récupérer ses zones WiFi
        $zones = WifiZone::where('proprio_id', $proprioId)->get();

        // 3. Envoyer les zones à la vue
        return view('proprio.wifizones', compact('zones'));
    }


     public function store(Request $request) 
    {
        try {
            // Validation des données entrantes
            $data = $request->validate([
                'nom_zone' => 'required|string|max:255',
                'adresse' => 'nullable|string|max:255',
            ]);

            // Récupération de l'ID du proprio connecté
            $proprioId = Auth::guard('proprio')->id();
            
            if (!$proprioId) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
            }

            // Création de la zone
            $zone = WifiZone::create([
                'proprio_id' => $proprioId,
                'nom_zone' => $data['nom_zone'],
                'adresse' => $data['adresse'],
                'token' => 'zone_' . Str::random(10),
            ]);

            return response()->json([
                'success' => true,
                'token' => $zone->token,
                'url' => url('/portal/login?z=' . $zone->token)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() // Ceci vous dira s'il y a une autre erreur
            ], 500);
        }
    }
}