<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table des configurations système.
 * 
 * Stocke les paramètres configurables de la plateforme
 * (taux commission, limites, etc.).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            
            // Clé unique de configuration
            $table->string('key')->unique();
            
            // Valeur (peut être string, number, boolean, json)
            $table->text('value');
            
            // Type de donnée pour faciliter le parsing
            $table->string('type')->default('string'); // string, number, boolean, json
            
            // Description de la configuration
            $table->text('description')->nullable();
            
            // Qui a modifié en dernier
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('superadmins')->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            
            // Index
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
