<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Utiliser une requête SQL brute pour renommer le champ
        DB::statement('ALTER TABLE forfaits CHANGE profil_mikrotik temps_limit VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir au nom original
        DB::statement('ALTER TABLE forfaits CHANGE temps_limit profil_mikrotik VARCHAR(255)');
    }
};
