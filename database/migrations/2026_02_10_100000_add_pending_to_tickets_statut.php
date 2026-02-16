<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Modifier le champ statut pour inclure 'pending'
            $table->enum('statut', ['libre', 'pending', 'vendu'])->default('libre')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Revenir à l'ancien format
            $table->enum('statut', ['libre', 'vendu'])->default('libre')->change();
        });
    }
};
