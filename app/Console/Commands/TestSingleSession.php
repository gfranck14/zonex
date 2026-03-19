<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\ClientSessionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TestSingleSession extends Command
{
    protected $signature = 'test:single-session {--email=test@example.com : Email de test}';
    protected $description = 'Tester le système de session unique';

    protected $sessionService;

    public function __construct(ClientSessionService $sessionService)
    {
        parent::__construct();
        $this->sessionService = $sessionService;
    }

    public function handle()
    {
        $this->info('🧪 Test du système de session unique');
        $this->line('');

        // 1. Créer un client de test
        $client = $this->createTestClient();
        $this->info('✅ Client de test créé : ' . $client->pseudo);

        // 2. Vérifier aucune session active
        $hasSession = $this->sessionService->hasActiveSession($client->id);
        $this->info('📋 Session active avant connexion : ' . ($hasSession ? 'OUI' : 'NON'));

        // 3. Simuler une première connexion
        $this->info('🔐 Simulation première connexion...');
        $session = $this->sessionService->createSession($client, '192.168.1.100', 'Test Browser');
        $this->info('✅ Session créée : ' . $session->session_id);

        // 4. Vérifier la session est active
        $hasSession = $this->sessionService->hasActiveSession($client->id);
        $this->info('📋 Session active après connexion : ' . ($hasSession ? 'OUI' : 'NON'));

        // 5. Obtenir les infos de session
        $sessionInfo = $this->sessionService->getSessionInfo($client->id);
        if ($sessionInfo) {
            $this->info('📊 Infos session :');
            $this->info('   IP : ' . $sessionInfo['ip_address']);
            $this->info('   Durée : ' . $sessionInfo['session_duration']);
        }

        // 6. Simuler une tentative de connexion multiple
        $this->line('');
        $this->info('🚫 Simulation tentative de connexion multiple...');
        $existingSession = $this->sessionService->getActiveSession($client->id);
        
        if ($existingSession) {
            $this->warn('⚠️  Connexion multiple bloquée !');
            $this->info('   IP existante : ' . $existingSession->ip_address);
            $this->info('   Dernière activité : ' . $existingSession->last_activity->format('Y-m-d H:i:s'));
        } else {
            $this->info('✅ Aucune session active trouvée (normal)');
        }

        // 7. Nettoyer la session
        $this->line('');
        $this->info('🔓 Nettoyage de la session...');
        $this->sessionService->invalidateAllClientSessions($client->id);

        // 8. Vérifier la session est bien nettoyée
        $hasSession = $this->sessionService->hasActiveSession($client->id);
        $this->info('📋 Session active après nettoyage : ' . ($hasSession ? 'OUI' : 'NON'));

        // 9. Supprimer le client de test
        $client->delete();
        $this->info('🗑️  Client de test supprimé');

        $this->line('');
        $this->info('🎉 Test terminé avec succès !');
        $this->info('📝 Le système de session unique fonctionne correctement.');

        return 0;
    }

    private function createTestClient()
    {
        // Créer un client de test s'il n'existe pas
        $client = Client::where('telephone', '001234567890')->first();
        
        if (!$client) {
            $client = Client::create([
                'nom_complet' => 'Client Test Session',
                'telephone' => '001234567890',
                'pseudo' => 'test_session_' . time(),
                'password' => Hash::make('password123'),
                'total_depense' => 0,
            ]);
        }

        return $client;
    }
}
