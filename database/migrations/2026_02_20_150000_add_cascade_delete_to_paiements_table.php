<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la contrainte FK en utilisant MySQL directement
        // D'abord, trouvons le nom de la contrainte actuelle
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = 'zonex_test' 
            AND TABLE_NAME = 'paiements' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        
        foreach ($foreignKeys as $fk) {
            $fkName = $fk->CONSTRAINT_NAME;
            // Supprimer toutes les contraintes FK qui référencent tickets
            if (strpos($fkName, 'ticket_id') !== false) {
                DB::statement("ALTER TABLE paiements DROP FOREIGN KEY `$fkName`");
                break;
            }
        }
        
        // Ajouter la nouvelle contrainte avec CASCADE
        DB::statement("ALTER TABLE paiements ADD CONSTRAINT FK_paiements_tickets_cascade FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE paiements DROP FOREIGN KEY IF EXISTS FK_paiements_tickets_cascade");
        DB::statement("ALTER TABLE paiements ADD CONSTRAINT FK_paiements_tickets_null FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE SET NULL");
    }
};
