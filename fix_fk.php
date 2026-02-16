<?php
// Script pour corriger la clé étrangère de la table retraits

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Ajouter la nouvelle contrainte de clé étrangère
    DB::statement('ALTER TABLE retraits ADD CONSTRAINT retraits_user_id_foreign FOREIGN KEY (user_id) REFERENCES proprio(id) ON DELETE CASCADE');
    
    echo "✓ Clé étrangère ajoutée avec succès!\n";
    echo "  Table: retraits\n";
    echo "  Colonne: user_id\n";
    echo "  Références: proprio(id)\n";
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
