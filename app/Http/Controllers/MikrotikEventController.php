<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour recevoir les webhooks Mikrotik Hotspot
 * 
 * Événement unique géré:
 * - logout: client déconnecté (avec cause et zone)
 * 
 * URL: POST /api/hotspot/logout
 */
class MikrotikEventController extends Controller
{
    /**
     * Endpoint unique pour les webhooks Mikrotik logout
     * URL: POST /api/hotspot/logout
     * Données attendues: user, cause, zone
     */
    public function handleLogoutWithZone(Request $request)
    {
        // === Validation des données requises ===
        $validated = $request->validate([
            'user'  => 'required|string',
            'cause' => 'required|string',
            'zone'  => 'required|string',
        ]);

        Log::info('🔴 Webhook logout Mikrotik reçu', $validated);

        // === RÉPONSE IMMÉDIATE À MIKROTIK ===
        // Mikrotik a une confirmation, le traitement continue en arrière-plan
        response('OK', 200)->send();
        
        // === TRAITEMENT EN ARRIÈRE-PLAN ===
        try {
            // Récupérer la zone via le token
            $wifizone = \App\Models\WifiZone::where('token', $validated['zone'])->first();
            
            if (!$wifizone) {
                Log::warning("❌ Zone non trouvée pour token: {$validated['zone']}");
                return; // Sortie silencieuse
            }

            // Récupérer les IDs des forfaits de cette zone
            $zoneForfaitIds = \App\Models\Forfait::where('wifizones_id', $wifizone->id)->pluck('id');
            
            Log::info("📍 Zone trouvée", [
                'zone_token' => $validated['zone'],
                'zone_id' => $wifizone->id,
                'zone_name' => $wifizone->nom_zone,
                'forfait_ids' => $zoneForfaitIds->toArray()
            ]);

            // Rechercher le ticket correspondant
            $ticket = \App\Models\Ticket::where('username', $validated['user'])
                ->whereIn('forfaits_id', $zoneForfaitIds)
                ->first();

            if (!$ticket) {
                Log::warning("❌ Ticket non trouvé", [
                    'user' => $validated['user'],
                    'zone' => $validated['zone'],
                    'forfait_ids_recherches' => $zoneForfaitIds->toArray()
                ]);
                return; // Sortie silencieuse
            }

            // Mettre à jour le champ logout_cause
            $ancienLogoutCause = $ticket->logout_cause;
            $ticket->update(['logout_cause' => $validated['cause']]);

            Log::info("✅ Ticket mis à jour avec succès", [
                'ticket_id' => $ticket->id,
                'username' => $ticket->username,
                'ancien_logout_cause' => $ancienLogoutCause,
                'nouveau_logout_cause' => $validated['cause'],
                'zone' => $wifizone->nom_zone,
                'forfait_id' => $ticket->forfaits_id
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ Erreur lors du traitement du webhook", [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
        }

        // === RETOUR APRÈS TRAITEMENT ===
        // La réponse a déjà été envoyée plus haut
        return;
    }
}
