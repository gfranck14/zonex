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

        // 2. Récupérer ses zones WiFi avec les forfaits et le décompte des tickets
        $zones = WifiZone::where('proprio_id', $proprioId)
            ->with(['forfaits' => function($query) {
                $query->withCount(['tickets as tickets_count' => function($q) {
                    $q->where('statut', 'libre');
                }]);
                $query->withCount(['tickets as sales_today_count' => function($q) {
                    $q->where('statut', 'vendu')
                      ->whereDate('date_vente', now());
                }]);
            }])
            ->get();

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
                'token' => 'wz_' . Str::random(10),
            ]);

            return response()->json([
                'success' => true,
                'token' => $zone->token,
                'url' => url('/portal/login?z=' . $zone->token . '&mac=$(mac)')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une zone WiFi
     */
    public function update(Request $request, $id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->firstOrFail();

            $data = $request->validate([
                'nom_zone' => 'required|string|max:255',
                'adresse' => 'nullable|string|max:255',
            ]);

            $zone->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Zone mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une zone WiFi
     */
    public function destroy($id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->firstOrFail();

            $zone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Zone supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère l'impact de la suppression d'une zone (nombre forfaits/tickets)
     */
    public function getImpact($id)
    {
        try {
            $zone = WifiZone::where('id', $id)
                ->where('proprio_id', Auth::guard('proprio')->id())
                ->with(['forfaits' => function($q) {
                    $q->withCount('tickets');
                }])
                ->firstOrFail();

            $forfaitsCount = $zone->forfaits->count();
            $ticketsCount = $zone->forfaits->sum('tickets_count');

            return response()->json([
                'success' => true,
                'forfaits_count' => $forfaitsCount,
                'tickets_count' => $ticketsCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Zone introuvable'], 404);
        }
    }
}