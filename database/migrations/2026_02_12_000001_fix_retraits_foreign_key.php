<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration corrige la clé étrangère de la table retraits
     * qui pointait vers 'users' au lieu de 'proprio'.
     */
    public function up(): void
    {
        // La table a été renommée de withdrawals à retraits
        // Mais la clé étrangère fait toujours référence à 'users' au lieu de 'proprio'
        
        // Étape 1: Supprimer l'ancienne clé étrangère si elle existe
        Schema::table('retraits', function (Blueprint $table) {
            // Chercher la clé étrangère
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'retraits' 
                AND CONSTRAINT_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME = 'users'
            ");
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $fk) {
                    // Utiliser DB::statement pour supprimer la contrainte
                    DB::statement("ALTER TABLE retraits DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                }
                Log::info('[Migration] Anciennes clés étrangères supprimées de retraits');
            }
        });

        // Étape 2: Ajouter la nouvelle clé étrangère vers proprio
        Schema::table('retraits', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('proprio')->onDelete('cascade');
        });

        Log::info('[Migration] Clé étrangère corrigée: retraits.user_id -> proprio.id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la nouvelle clé étrangère
        Schema::table('retraits', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'retraits' 
                AND CONSTRAINT_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME = 'proprio'
            ");
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $fk) {
                    DB::statement("ALTER TABLE retraits DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                }
            }
        });

        // Restaurer l'ancienne clé étrangère vers users
        Schema::table('retraits', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
