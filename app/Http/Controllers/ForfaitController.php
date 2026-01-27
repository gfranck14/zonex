<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forfait;
use App\Models\WifiZone;
use Illuminate\Support\Facades\Auth;

class ForfaitController extends Controller
{
    /**
     * Store a newly created forfait
     */
    public function store(Request $request)
    {
        $request->validate([
            'wifi_zone_id' => 'required|exists:wifizones,id',
            'profile_name' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
            'validite' => 'required|integer|min:1',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Vérifier que la zone appartient au proprio connecté
        $zone = WifiZone::where('id', $request->wifi_zone_id)
            ->where('proprio_id', Auth::guard('proprio')->id())
            ->first();

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone non trouvée ou non autorisée'
            ], 403);
        }

        $forfait = Forfait::create([
            'wifizones_id' => $request->wifi_zone_id,
            'profile_name' => $request->profile_name,
            'time_limit' => $request->time_limit,
            'validite' => $request->validite,
            'prix_vente' => $request->prix_vente,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Forfait créé avec succès',
            'forfait' => $forfait
        ]);
    }

    /**
     * Update the specified forfait
     */
    public function update(Request $request, $id)
    {
        $forfait = Forfait::findOrFail($id);

        // Vérifier que le forfait appartient à une zone du proprio connecté
        $zone = WifiZone::where('id', $forfait->wifizones_id)
            ->where('proprio_id', Auth::guard('proprio')->id())
            ->first();

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Forfait non trouvé ou non autorisé'
            ], 403);
        }

        $request->validate([
            'profile_name' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
            'validite' => 'required|integer|min:1',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        $forfait->update([
            'profile_name' => $request->profile_name,
            'time_limit' => $request->time_limit,
            'validite' => $request->validite,
            'prix_vente' => $request->prix_vente,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Forfait mis à jour avec succès',
            'forfait' => $forfait
        ]);
    }

    /**
     * Remove the specified forfait
     */
    public function destroy($id)
    {
        $forfait = Forfait::findOrFail($id);

        // Vérifier que le forfait appartient à une zone du proprio connecté
        $zone = WifiZone::where('id', $forfait->wifizones_id)
            ->where('proprio_id', Auth::guard('proprio')->id())
            ->first();

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Forfait non trouvé ou non autorisé'
            ], 403);
        }

        $forfait->delete();

        return response()->json([
            'success' => true,
            'message' => 'Forfait supprimé avec succès'
        ]);
    }

    /**
     * Get forfait details for editing
     */
    public function edit($id)
    {
        $forfait = Forfait::with('wifiZone')->findOrFail($id);

        // Vérifier que le forfait appartient à une zone du proprio connecté
        $zone = WifiZone::where('id', $forfait->wifizones_id)
            ->where('proprio_id', Auth::guard('proprio')->id())
            ->first();

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Forfait non trouvé ou non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'forfait' => $forfait
        ]);
    }
}
