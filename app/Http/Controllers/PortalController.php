<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WifiZone;
use App\Models\Ticket;
use App\Models\Forfait;
use App\Models\BlockedClient;

class PortalController extends Controller
{
    /**
     * Afficher la page de connexion du portail captif
     */
    public function showLogin(Request $request)
    {
        $token = $request->get('z');
        
        if (!$token) {
            abort(404, 'Zone non spécifiée');
        }

        $zone = WifiZone::where('token', $token)->first();
        
        if (!$zone) {
            abort(404, 'Zone non trouvée');
        }

        return view('proprio.portal_login', compact('zone'));
    }

    /**
     * Authentifier un utilisateur sur le portail
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'zone_token' => 'required|string',
        ]);

        // Vérifier que la zone existe
        $zone = WifiZone::where('token', $request->zone_token)->first();
        
        if (!$zone) {
            return back()->withErrors(['login' => 'Zone non valide']);
        }

        // Chercher le ticket correspondant
        $ticket = Ticket::where('username', $request->username)
            ->where('password', $request->password)
            ->where('forfait_id', function($query) use ($zone) {
                $query->select('id')
                    ->from('forfaits')
                    ->where('wifizones_id', $zone->id);
            })
            ->where('status', 'available')
            ->first();

        if (!$ticket) {
            return back()->withErrors(['login' => 'Identifiants incorrects ou ticket déjà utilisé']);
        }

        // Vérifier si le client est bloqué par ce propriétaire
        if ($ticket->client_id && BlockedClient::isBlocked($ticket->client_id, $zone->proprio_id)) {
            return back()->withErrors(['login' => 'Vous êtes bloqué sur cette zone. Veuillez contacter le support.']);
        }

        // Marquer le ticket comme utilisé
        $ticket->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by_ip' => $request->ip(),
        ]);

        // Rediriger vers la page de succès ou vers l'URL de redirection
        return redirect()->away($request->get('redir', 'https://google.com'));
    }

    /**
     * Page de succès après connexion
     */
    public function success()
    {
        return view('proprio.portal_success');
    }

    /**
     * API pour vérifier un ticket
     */
    public function checkTicket(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'zone_token' => 'required|string',
        ]);

        $zone = WifiZone::where('token', $request->zone_token)->first();
        
        if (!$zone) {
            return response()->json(['valid' => false, 'message' => 'Zone non valide']);
        }

        $ticket = Ticket::where('username', $request->username)
            ->where('password', $request->password)
            ->where('forfait_id', function($query) use ($zone) {
                $query->select('id')
                    ->from('forfaits')
                    ->where('wifizones_id', $zone->id);
            })
            ->where('status', 'available')
            ->first();

        if ($ticket) {
            return response()->json([
                'valid' => true,
                'forfait' => $ticket->forfait,
                'message' => 'Ticket valide'
            ]);
        }

        return response()->json(['valid' => false, 'message' => 'Ticket invalide ou déjà utilisé']);
    }
}
