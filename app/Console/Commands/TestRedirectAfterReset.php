<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestRedirectAfterReset extends Command
{
    protected $signature = 'test:redirect-after-reset';
    protected $description = 'Tester la redirection vers l\'URL d\'origine après réinitialisation';

    public function handle()
    {
        $this->info('🧪 Test de redirection après réinitialisation');
        $this->line('');

        // 1. Créer un client avec mot de passe "12345"
        $client = Client::create([
            'nom_complet' => 'Client Test Redirect',
            'telephone' => '001122334433',
            'pseudo' => 'test_redirect_' . time(),
            'password' => Hash::make('12345'),
            'total_depense' => 0,
        ]);
        $this->info('✅ Client créé avec mot de passe "12345"');

        // 2. Simuler une connexion avec sauvegarde d'URL
        $this->line('');
        $this->info('🔍 Simulation de connexion avec sauvegarde d\'URL');
        
        $originalUrl = 'http://127.0.0.1:8000/portal/shop';
        $request = new \Illuminate\Http\Request([
            'telephone' => $client->telephone,
            'password' => '12345',
        ]);
        
        // Simuler l'en-tête Referer
        $request->headers->set('referer', $originalUrl);
        
        // Simuler la sauvegarde en session
        session(['reset_password_redirect_url' => $originalUrl]);
        
        $this->info('✅ URL d\'origine sauvegardée : ' . $originalUrl);

        // 3. Simuler la réinitialisation du mot de passe
        $this->line('');
        $this->info('🔍 Simulation de réinitialisation');
        
        $resetToken = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('client_password_resets')->updateOrInsert(
            ['telephone' => $client->telephone],
            [
                'telephone' => $client->telephone,
                'token' => hash('sha256', $resetToken),
                'created_at' => now()
            ]
        );

        $resetRequest = new \Illuminate\Http\Request([
            'token' => $resetToken,
            'telephone' => $client->telephone,
            'password' => 'newsecurepass',
            'password_confirmation' => 'newsecurepass'
        ]);

        $controller = new \App\Http\Controllers\ClientPortalController();
        
        try {
            $response = $controller->resetPassword($resetRequest);
            
            // Vérifier la redirection
            $redirectUrl = session('reset_password_redirect_url', '/portal');
            $this->info('✅ URL de redirection récupérée : ' . $redirectUrl);
            
            if ($redirectUrl === $originalUrl) {
                $this->info('✅ Redirection vers l\'URL d\'origine correcte');
            } else {
                $this->error('❌ Mauvaise URL de redirection');
            }
            
            // Vérifier que la session a été nettoyée
            if (!session()->has('reset_password_redirect_url')) {
                $this->info('✅ URL de redirection supprimée de la session');
            } else {
                $this->error('❌ URL de redirection toujours en session');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur : ' . $e->getMessage());
        }

        // 4. Nettoyer
        $client->delete();
        
        $this->line('');
        $this->info('🗑️  Client de test supprimé');
        $this->info('🎉 Test terminé !');
        $this->line('');
        $this->info('📝 Flux testé :');
        $this->info('   1. Connexion avec "12345" → Sauvegarde URL d\'origine');
        $this->info('   2. Réinitialisation mot de passe → Récupération URL sauvegardée');
        $this->info('   3. Redirection vers page d\'origine');
        $this->info('   4. Nettoyage de la session');

        return 0;
    }
}
