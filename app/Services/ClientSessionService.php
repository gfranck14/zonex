<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ClientSessionService
{
    /**
     * Vérifier si un client a déjà une session active
     */
    public function hasActiveSession($clientId, $timeoutMinutes = 30)
    {
        return ClientSession::where('client_id', $clientId)
            ->where('last_activity', '>', now()->subMinutes($timeoutMinutes))
            ->exists();
    }

    /**
     * Obtenir la session active d'un client
     */
    public function getActiveSession($clientId, $timeoutMinutes = 30)
    {
        return ClientSession::where('client_id', $clientId)
            ->where('last_activity', '>', now()->subMinutes($timeoutMinutes))
            ->first();
    }

    /**
     * Enregistrer une nouvelle session client
     */
    public function createSession(Client $client, $ipAddress, $userAgent = null)
    {
        // Nettoyer les anciennes sessions expirées
        $this->cleanupExpiredSessions();

        // Créer la nouvelle session
        $session = ClientSession::create([
            'client_id' => $client->id,
            'session_id' => Session::getId(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_activity' => now(),
        ]);

        Log::info('Nouvelle session client créée', [
            'client_id' => $client->id,
            'session_id' => $session->session_id,
            'ip_address' => $ipAddress,
        ]);

        return $session;
    }

    /**
     * Mettre à jour l'activité d'une session
     */
    public function updateSessionActivity($sessionId)
    {
        ClientSession::where('session_id', $sessionId)
            ->update(['last_activity' => now()]);
    }

    /**
     * Invalider toutes les sessions d'un client
     */
    public function invalidateAllClientSessions($clientId)
    {
        $sessions = ClientSession::where('client_id', $clientId)->get();
        
        foreach ($sessions as $session) {
            // Invalider la session Laravel
            $this->invalidateLaravelSession($session->session_id);
            
            Log::info('Session client invalidée', [
                'client_id' => $clientId,
                'session_id' => $session->session_id,
            ]);
        }

        // Supprimer les enregistrements
        ClientSession::where('client_id', $clientId)->delete();
    }

    /**
     * Invalider une session spécifique
     */
    public function invalidateSession($sessionId)
    {
        $this->invalidateLaravelSession($sessionId);
        ClientSession::where('session_id', $sessionId)->delete();
    }

    /**
     * Nettoyer les sessions expirées
     */
    public function cleanupExpiredSessions($timeoutMinutes = 30)
    {
        $expiredCount = ClientSession::where('last_activity', '<=', now()->subMinutes($timeoutMinutes))
            ->delete();

        if ($expiredCount > 0) {
            Log::info('Sessions expirées nettoyées', ['count' => $expiredCount]);
        }

        return $expiredCount;
    }

    /**
     * Invalider une session Laravel
     */
    private function invalidateLaravelSession($sessionId)
    {
        DB::table('sessions')
            ->where('id', $sessionId)
            ->delete();
    }

    /**
     * Obtenir les informations de session active
     */
    public function getSessionInfo($clientId)
    {
        $session = $this->getActiveSession($clientId);
        
        if (!$session) {
            return null;
        }

        return [
            'ip_address' => $session->ip_address,
            'user_agent' => $session->user_agent,
            'last_activity' => $session->last_activity,
            'session_duration' => $session->created_at->diffForHumans(now()),
        ];
    }
}
