<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class TestShopProtection extends Command
{
    protected $signature = 'test:shop-protection';
    protected $description = 'Tester la protection de la route /portal/shop';

    public function handle()
    {
        $this->info('🧪 Test de protection de la route /portal/shop');
        $this->line('');

        // 1. Vérifier que la route existe
        $route = Route::getRoutes()->getByName('client.shop');
        if (!$route) {
            $this->error('❌ Route client.shop non trouvée');
            return 1;
        }
        
        $this->info('✅ Route client.shop trouvée : ' . $route->uri());
        
        // 2. Vérifier les middlewares
        $middlewares = $route->middleware();
        $this->info('📋 Middlewares appliqués :');
        foreach ($middlewares as $middleware) {
            $this->info('   - ' . $middleware);
        }

        // 3. Vérifier la présence du middleware d'authentification
        $hasAuth = in_array('auth:client', $middlewares);
        $hasRedirect = in_array('redirect.if.not.client', $middlewares);

        if ($hasAuth) {
            $this->info('✅ Middleware auth:client présent');
        } else {
            $this->error('❌ Middleware auth:client manquant');
        }

        if ($hasRedirect) {
            $this->info('✅ Middleware redirect.if.not.client présent');
        } else {
            $this->error('❌ Middleware redirect.if.not.client manquant');
        }

        $this->line('');
        $this->info('🔍 Scénarios de test :');
        $this->line('');

        // Scénario 1: Accès direct sans être connecté
        $this->info('1️⃣  Accès direct sans authentification :');
        $this->info('   URL : GET /portal/shop');
        $this->info('   Attendu : Redirection vers /portal avec message d\'erreur');
        $this->info('   Status HTTP : 302 (Found)');

        // Scénario 2: Accès AJAX sans être connecté
        $this->info('');
        $this->info('2️⃣  Accès AJAX sans authentification :');
        $this->info('   Headers : X-Requested-With: XMLHttpRequest');
        $this->info('   Attendu : JSON avec message d\'erreur');
        $this->info('   Status HTTP : 401 (Unauthorized)');

        // Scénario 3: Accès en étant connecté
        $this->info('');
        $this->info('3️⃣  Accès en étant connecté :');
        $this->info('   Condition : Client authentifié');
        $this->info('   Attendu : Accès autorisé à la page shop');
        $this->info('   Status HTTP : 200 (OK)');

        $this->line('');
        $this->info('📝 Instructions de test manuel :');
        $this->line('');
        $this->info('Pour tester manuellement :');
        $this->info('1. Ouvrez un navigateur en mode privé');
        $this->info('2. Allez directement sur : http://votre-domaine.com/portal/shop');
        $this->info('3. Vous devriez être redirigé vers la page d\'accueil du portail');
        $this->info('4. Le message "Vous devez être connecté pour accéder à cette page." devrait s\'afficher');
        $this->line('');
        $this->info('5. Connectez-vous avec un compte client valide');
        $this->info('6. Accédez à nouveau à : /portal/shop');
        $this->info('7. Vous devriez maintenant voir la page shop normalement');

        $this->line('');
        $this->info('🔒 Routes protégées supplémentaires :');
        $protectedRoutes = [
            'client.buy' => '/portal/buy/{forfait}',
            'client.ticket' => '/portal/ticket/{ticket}',
            'client.logout' => '/portal/logout',
            'fedapay.form' => '/portal/payment/{forfait}',
            'fedapay.checkout.no-phone' => '/portal/payment-no-phone/{forfait}',
            'fedapay.checkout' => '/portal/checkout/{forfait}',
        ];

        foreach ($protectedRoutes as $routeName => $uri) {
            $route = Route::getRoutes()->getByName($routeName);
            if ($route) {
                $this->info('✅ ' . $routeName . ' : ' . $uri);
            } else {
                $this->warn('⚠️  ' . $routeName . ' : Route non trouvée');
            }
        }

        $this->line('');
        if ($hasAuth && $hasRedirect) {
            $this->info('🎉 La protection de la route /portal/shop est correctement configurée !');
            return 0;
        } else {
            $this->error('❌ La protection est incomplète. Vérifiez les middlewares.');
            return 1;
        }
    }
}
