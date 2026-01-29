<?php

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => $_ENV['DB_CONNECTION'],
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Récupérer toutes les tables de la base de données
$tables = $capsule->getDatabaseManager()->select("SHOW TABLES");

$exportContent = "-- Export de la base de données zonex_test\n";
$exportContent .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($tables as $tableObj) {
    $table = reset($tableObj);
    
    // Récupérer le schéma de la table
    $schema = $capsule->getDatabaseManager()->select("SHOW CREATE TABLE `$table`")[0];
    $exportContent .= $schema->{'Create Table'} . ";\n\n";
    
    // Récupérer les données de la table
    $data = $capsule->table($table)->get();
    
    if ($data->count() > 0) {
        $columns = $capsule->getDatabaseManager()->select("DESCRIBE `$table`");
        $columnNames = array_column($columns, 'Field');
        
        $exportContent .= "INSERT INTO `$table` (`" . implode('`, `', $columnNames) . "`) VALUES\n";
        
        $rows = [];
        foreach ($data as $row) {
            $values = [];
            foreach ($columnNames as $column) {
                $value = $row->$column;
                
                if (is_null($value)) {
                    $values[] = 'NULL';
                } elseif (is_bool($value)) {
                    $values[] = $value ? 1 : 0;
                } elseif (is_string($value)) {
                    $values[] = "'" . $capsule->getDatabaseManager()->getPdo()->quote($value) . "'";
                } else {
                    $values[] = $value;
                }
            }
            $rows[] = "(" . implode(', ', $values) . ")";
        }
        
        $exportContent .= implode(",\n", $rows) . ";\n\n";
    }
}

// Écrire le contenu dans un fichier
$fileName = 'database/dumps/zonex_test_' . date('Ymd_His') . '.sql';
file_put_contents($fileName, $exportContent);

echo "Exportation réussie : $fileName\n";
?>