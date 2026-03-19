<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestResetPassword extends Command
{
    protected $signature = 'test:reset-password {client_id?}';
    protected $description = 'Tester la réinitialisation du mot de passe client à 12345';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        
        if ($clientId) {
            // Test avec un client spécifique
            $this->testSpecificClient($clientId);
        } else {
            // Test avec un client de test
            $this->testWithTestClient();
        }

        return 0;
    }

    private function testSpecificClient($clientId)
    {
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("❌ Client #{$clientId} non trouvé");
            return;
        }

        $this->info("🧪 Test de réinitialisation du mot de passe pour le client #{$clientId}");
        $this->info("   Téléphone : " . $client->telephone);
        $this->info("   Nom : " . $client->nom_complet);
        
        // Mémoriser l'ancien mot de passe hashé
        $oldPasswordHash = $client->password;
        
        // Simuler la réinitialisation
        $newPassword = '12345';
        $hashedPassword = Hash::make($newPassword);
        
        $client->update(['password' => $hashedPassword]);
        
        $this->info("✅ Mot de passe réinitialisé avec succès");
        $this->info("   Nouveau mot de passe : 12345");
        $this->info("   Hash généré : " . substr($hashedPassword, 0, 20) . "...");
        
        // Vérifier que le hash a bien changé
        if ($oldPasswordHash !== $client->password) {
            $this->info("✅ Le mot de passe a bien été modifié en base de données");
        } else {
            $this->error("❌ Le mot de passe n'a pas changé");
        }
        
        // Tester la vérification
        if (Hash::check('12345', $client->password)) {
            $this->info("✅ Le nouveau mot de passe '12345' est valide");
        } else {
            $this->error("❌ Le nouveau mot de passe '12345' est invalide");
        }
    }

    private function testWithTestClient()
    {
        $this->info("🧪 Test avec un client de test");
        
        // Créer un client de test
        $testClient = Client::create([
            'nom_complet' => 'Client Test Reset',
            'telephone' => '001122334455',
            'pseudo' => 'test_reset_' . time(),
            'password' => Hash::make('oldpassword'),
            'total_depense' => 0,
        ]);

        $this->info("✅ Client de test créé : #" . $testClient->id);
        
        // Test de réinitialisation
        $this->testSpecificClient($testClient->id);
        
        // Nettoyer
        $testClient->delete();
        $this->info("🗑️  Client de test supprimé");
    }
}
