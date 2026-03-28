<?php

namespace App\Http\Controllers;

use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\WifiZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Verify that the user owns the zone/forfait.
     */
    private function verifyOwnership($zoneId)
    {
        return WifiZone::where('id', $zoneId)
            ->where('proprio_id', Auth::guard('proprio')->id())
            ->exists();
    }

    /**
     * Get preview of tickets to be deleted (for confirmation modal).
     */
    public function previewDelete(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        $data = [
            'libre_count' => 0,
            'vendu_count' => 0,
            'total_count' => 0,
            'warning' => null
        ];

        if ($type === 'forfait') {
            $forfait = Forfait::findOrFail($id);
            // Verify ownership
            if (!$this->verifyOwnership($forfait->wifizones_id)) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            $tickets = Ticket::where('forfaits_id', $id);
            $data['libre_count'] = $tickets->clone()->where('statut', 'libre')->count();
            $data['vendu_count'] = $tickets->clone()->where('statut', 'vendu')->count();
            $data['total_count'] = $tickets->count();
            $data['forfait_name'] = $forfait->nom;
        } elseif ($type === 'zone') {
            $zone = WifiZone::findOrFail($id);
            if (!$this->verifyOwnership($id)) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            $forfaits = Forfait::where('wifizones_id', $id)->pluck('id');
            $tickets = Ticket::whereIn('forfaits_id', $forfaits);
            $data['libre_count'] = $tickets->clone()->where('statut', 'libre')->count();
            $data['vendu_count'] = $tickets->clone()->where('statut', 'vendu')->count();
            $data['total_count'] = $tickets->count();
            $data['zone_name'] = $zone->nom_zone;
        } elseif ($type === 'date') {
            $date = $request->get('date');
            $zoneId = $request->get('zone_id');

            if ($zoneId) {
                if (!$this->verifyOwnership($zoneId)) {
                    return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
                }
                $forfaits = Forfait::where('wifizones_id', $zoneId)->pluck('id');
                $tickets = Ticket::whereIn('forfaits_id', $forfaits)->whereDate('created_at', $date);
            } else {
                // Admin mode - all zones
                $tickets = Ticket::whereDate('created_at', $date);
            }

            $data['libre_count'] = $tickets->clone()->where('statut', 'libre')->count();
            $data['vendu_count'] = $tickets->clone()->where('statut', 'vendu')->count();
            $data['total_count'] = $tickets->count();
            $data['date'] = $date;
        }

        // Add warning if there are sold tickets
        if ($data['vendu_count'] > 0) {
            $data['warning'] = "Attention: {$data['vendu_count']} ticket(s) ont déjà été vendus et seront supprimés.";
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Delete only unsold tickets (statut = 'libre') for a specific forfait.
     */
    public function deleteByForfait(Request $request, $forfaitId)
    {
        $forfait = Forfait::findOrFail($forfaitId);

        // Verify ownership
        if (!$this->verifyOwnership($forfait->wifizones_id)) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
        $usernames = Ticket::where('forfaits_id', $forfaitId)->where('statut', 'libre')->pluck('username')->toArray();
        if (!empty($usernames)) {
            try {
                $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                $mikrotikService->removeUsersByUsernames($forfait->wifizone, $usernames);
            } catch (\Exception $e) {
                \Log::warning("⚠️ Échec suppression MikroTik lors de deleteByForfait: " . $e->getMessage());
            }
        }

        $count = count($usernames);
        Ticket::where('forfaits_id', $forfaitId)->where('statut', 'libre')->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} ticket(s) non vendu(s) supprimé(s) pour le forfait '{$forfait->nom}'.",
        ]);
    }

    /**
     * Delete all tickets for a specific zone (all forfaits).
     */
    public function deleteByZone(Request $request, $zoneId)
    {
        $zone = WifiZone::findOrFail($zoneId);

        // Verify ownership
        if (!$this->verifyOwnership($zoneId)) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $ticketsToDelete = Ticket::whereIn('forfaits_id', $forfaits);
        $usernames = $ticketsToDelete->pluck('username')->toArray();
        $count = count($usernames);
        $venduCount = $ticketsToDelete->clone()->where('statut', 'vendu')->count();

        // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
        if (!empty($usernames)) {
            try {
                $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                $mikrotikService->removeUsersByUsernames($zone, $usernames);
            } catch (\Exception $e) {
                \Log::warning("⚠️ Échec suppression MikroTik lors de deleteByZone: " . $e->getMessage());
            }
        }

        Ticket::whereIn('forfaits_id', $forfaits)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} ticket(s) supprimé(s) pour la zone '{$zone->nom_zone}'.",
            'warning' => $venduCount > 0 ? "{$venduCount} ticket(s) vendu(s) ont été supprimés." : null
        ]);
    }

    /**
     * Delete all tickets created on a specific date.
     */
    public function deleteByDate(Request $request)
    {
        $date = $request->get('date');
        $zoneId = $request->get('zone_id');

        if ($zoneId) {
            if (!$this->verifyOwnership($zoneId)) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            $zone = WifiZone::find($zoneId);
            $forfaits = Forfait::where('wifizones_id', $zoneId)->pluck('id');
            $ticketsToDelete = Ticket::whereIn('forfaits_id', $forfaits)->whereDate('created_at', $date);
            
            $usernames = $ticketsToDelete->pluck('username')->toArray();
            $count = count($usernames);
            $venduCount = $ticketsToDelete->clone()->where('statut', 'vendu')->count();

            // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
            if ($zone && !empty($usernames)) {
                try {
                    $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                    $mikrotikService->removeUsersByUsernames($zone, $usernames);
                } catch (\Exception $e) {
                    \Log::warning("⚠️ Échec suppression MikroTik lors de deleteByDate: " . $e->getMessage());
                }
            }

            Ticket::whereIn('forfaits_id', $forfaits)->whereDate('created_at', $date)->delete();
        } else {
            // Suppression globale restreinte au propriétaire (Audit Point)
            $proprioId = Auth::guard('proprio')->id();
            $ticketsToDelete = Ticket::whereDate('created_at', $date)
                ->whereHas('forfait.wifizone', function($q) use ($proprioId) {
                    $q->where('proprio_id', $proprioId);
                });
            
            $usernames = $ticketsToDelete->pluck('username')->toArray();
            $count = count($usernames);
            $venduCount = $ticketsToDelete->clone()->where('statut', 'vendu')->count();
            
            // Note: Une suppression globale nécessiterait d'itérer sur chaque zone concernée pour MikroTik.
            // Pour l'instant, on se contente de sécuriser la suppression locale.
            
            $ticketsToDelete->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} ticket(s) supprimé(s) pour la date du {$date}.",
            'warning' => $venduCount > 0 ? "{$venduCount} ticket(s) vendu(s) ont été supprimés." : null
        ]);
    }

    /**
     * Delete tickets by import batch ID.
     */
    public function deleteByBatch(Request $request, $batchId)
    {
        $ticket = Ticket::where('import_batch_id', $batchId)->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Batch non trouvé'], 404);
        }

        $forfait = Forfait::find($ticket->forfaits_id);
        if ($forfait && !$this->verifyOwnership($forfait->wifizone->id)) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $ticketsToDelete = Ticket::where('import_batch_id', $batchId);
        $usernames = $ticketsToDelete->pluck('username')->toArray();
        $count = count($usernames);
        $venduCount = $ticketsToDelete->clone()->where('statut', 'vendu')->count();

        // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
        if ($forfait && !empty($usernames)) {
            try {
                $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                $mikrotikService->removeUsersByUsernames($forfait->wifizone, $usernames);
            } catch (\Exception $e) {
                \Log::warning("⚠️ Échec suppression MikroTik lors de deleteByBatch: " . $e->getMessage());
            }
        }

        Ticket::where('import_batch_id', $batchId)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} ticket(s) supprimé(s) pour le batch '{$batchId}'.",
            'warning' => $venduCount > 0 ? "{$venduCount} ticket(s) vendu(s) ont été supprimés." : null
        ]);
    }
}
