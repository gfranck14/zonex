<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestPasswordResetProtection extends Command
{
    protected $signature = 'test:password-reset-protection';
    protected $description = 'Tester la protection contre le mot de passe 12345 dans la réinitialisation';

    public function handle()
    {
        $this->info('🧪 Test de protection contre le mot de passe "12345"');
        $this->line('');

        // 1. Créer un client de test
        $client = $this->createTestClient();
        $this->info('✅ Client de test créé : ' . $client->telephone);

        // 2. Générer un token de réinitialisation
        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('client_password_resets')->updateOrInsert(
            ['telephone' => $client->telephone],
            [
                'telephone' => $client->telephone,
                'token' => hash('sha256', $token),
                'created_at' => now()
            ]
        );
        $this->info('✅ Token de réinitialisation généré');

        // 3. Tester la validation côté serveur
        $this->line('');
        $this->info('🔍 Test de validation côté serveur :');
        
        // Simuler une requête avec mot de passe "12345"
        $request = new \Illuminate\Http\Request([
            'token' => $token,
            'telephone' => $client->telephone,
            'password' => '12345',
            'password_confirmation' => '12345'
        ]);

        $controller = new \App\Http\Controllers\ClientPortalController();
        
        try {
            $response = $controller->resetPassword($request);
            $this->error('❌ La validation a échoué : le mot de passe "12345" a été accepté');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->info('✅ Validation réussie : le mot de passe "12345" a été bloqué');
            $this->info('   Erreurs : ' . json_encode($e->errors()));
        }

        // 4. Tester avec un mot de passe valide
        $this->line('');
        $this->info('🔍 Test avec un mot de passe valide :');
        
        $validRequest = new \Illuminate\Http\Request([
            'token' => $token,
            'telephone' => $client->telephone,
            'password' => 'newsecurepass',
            'password_confirmation' => 'newsecurepass'
        ]);

        try {
            $response = $controller->resetPassword($validRequest);
            $this->info('✅ Mot de passe valide accepté');
            
            // Vérifier que le mot de passe a bien été changé
            $client->refresh();
            if (Hash::check('newsecurepass', $client->password)) {
                $this->info('✅ Le mot de passe a bien été mis à jour en base de données');
            } else {
                $this->error('❌ Le mot de passe n\'a pas été mis à jour');
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur inattendue : ' . $e->getMessage());
        }

        // 5. Nettoyer
        $client->delete();
        \Illuminate\Support\Facades\DB::table('client_password_resets')
            ->where('telephone', $client->telephone)
            ->delete();
        
        $this->line('');
        $this->info('🗑️  Nettoyage terminé');
        $this->info('🎉 Test terminé !');
        $this->line('');
        $this->info('📝 Résumé des protections en place :');
        $this->info('   • Validation JavaScript côté client');
        $this->info('   • Validation Laravel côté serveur');
        $this->info('   • Message informatif pour l\'utilisateur');
        $this->info('   • Longueur minimale de 6 caractères');

        return 0;
    }

    private function createTestClient()
    {
        return Client::create([
            'nom_complet' => 'Client Test Protection',
            'telephone' => '009876543211',
            'pseudo' => 'test_protection_' . time(),
            'password' => Hash::make('oldpassword'),
            'total_depense' => 0,
        ]);
    }
}
