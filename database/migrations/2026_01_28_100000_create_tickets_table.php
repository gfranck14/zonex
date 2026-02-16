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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le forfait (catégorie)
            $table->foreignId('forfaits_id')->constrained('forfaits')->onDelete('cascade');
            
            // Identifiants du ticket
            $table->string('username')->unique();
            $table->string('password')->nullable();
            
            // État du ticket
            $table->string('statut')->default('libre'); // libre / vendu
            
            // Lien vers le client (optionnel)
            $table->foreignId('client_id')->nullable()->constrained('clients');
            
            // Date de vente
            $table->timestamp('date_vente')->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
