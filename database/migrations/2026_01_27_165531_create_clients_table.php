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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet')->nullable(); // Ex: John Doe
            $table->string('telephone')->unique();     // Clé principale pour Mobile Money
            $table->string('mac_address')->nullable(); // Pour identifier l'appareil
            $table->integer('total_depense')->default(0); // Cache pour performance
            $table->string('derniere_zone')->nullable();  // Nom de la zone
            $table->boolean('is_blocked')->default(false); // Statut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
