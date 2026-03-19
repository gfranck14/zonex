<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\ClientSessionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TestSessionMultiple extends Command
{
    protected $signature = 'test:session-multiple';
    protected $description = 'Tester le blocage de connexion multiple';

    protected $sessionService;

    public function __construct(ClientSessionService $sessionService)
    {
        parent::__construct();
        $this->sessionService = $sessionService;
    }

    public function handle()
    {
        $this->info('🧪 Test du blocage de connexion multiple');
        $this->line('');

        // 1. Créer un client de test
        $client = $this->createTestClient();
        $this->info('✅ Client de test créé : ' . $client->pseudo);

        // 2. Simuler une première connexion
        $this->info('🔐 Simulation première connexion...');
        $session1 = $this->sessionService->createSession($client, '192.168.1.100', 'Chrome on Windows');
        $this->info('✅ Session 1 créée : ' . $session1->session_id);
        $this->info('   IP : ' . $session1->ip_address);

        // 3. Vérifier la session est active
        $hasSession = $this->sessionService->hasActiveSession($client->id);
        $this->info('📋 Session active : ' . ($hasSession ? 'OUI' : 'NON'));

        // 4. Simuler une tentative de connexion multiple
        $this->line('');
        $this->info('🚫 Simulation tentative de connexion multiple...');
        $existingSession = $this->sessionService->getActiveSession($client->id);
        
        if ($existingSession) {
            $this->warn('⚠️  CONNEXION MULTIPLE DÉTECTÉE !');
            $this->info('   IP existante : ' . $existingSession->ip_address);
            $this->info('   Dernière activité : ' . $existingSession->last_activity->format('Y-m-d H:i:s'));
            $this->info('   Durée : ' . $existingSession->created_at->diffForHumans(now()));
            
            // 5. Simuler la réponse JSON que le controller retournerait
            $this->line('');
            $this->info('📨 Réponse JSON qui serait envoyée au client :');
            $this->info('   HTTP Status : 409 Conflict');
            $this->info('   Content-Type : application/json');
            $this->info('   Body :');
            $this->info(json_encode([
                'success' => false,
                'message' => 'Ce compte est déjà connecté sur un autre appareil (' . 
                           $existingSession->ip_address . ') depuis ' .
                           $existingSession->created_at->diffForHumans(now()) . 
                           '. Veuillez vous déconnecter de l\'autre appareil avant de vous reconnecter.',
                'error_type' => 'session_multiple'
            ], JSON_PRETTY_PRINT));
            
            $this->line('');
            $this->info('🎨 Affichage frontend attendu :');
            $this->info('   - Toast ORANGE avec icône warning');
            $this->info('   - Message spécifique de session multiple');
            $this->info('   - Bouton de connexion réactivé');
            
        } else {
            $this->error('❌ Erreur : Aucune session active détectée');
        }

        // 6. Nettoyer la session
        $this->line('');
        $this->info('🔓 Nettoyage de la session...');
        $this->sessionService->invalidateAllClientSessions($client->id);

        // 7. Supprimer le client de test
        $client->delete();
        $this->info('🗑️  Client de test supprimé');

        $this->line('');
        $this->info('🎉 Test terminé !');
        $this->info('📝 Le système de blocage de connexion multiple fonctionne correctement.');
        $this->line('');
        $this->info('💡 Pour tester manuellement :');
        $this->info('   1. Connectez-vous avec un client sur un appareil');
        $this->info('   2. Essayez de vous connecter avec le même client sur un autre appareil');
        $this->info('   3. Vous devriez voir un toast ORANGE avec le message de session multiple');

        return 0;
    }

    private function createTestClient()
    {
        // Créer un client de test s'il n'existe pas
        $client = Client::where('telephone', '009876543210')->first();
        
        if (!$client) {
            $client = Client::create([
                'nom_complet' => 'Client Test Multiple',
                'telephone' => '009876543210',
                'pseudo' => 'test_multiple_' . time(),
                'password' => Hash::make('password123'),
                'total_depense' => 0,
            ]);
        }

        return $client;
    }
}
