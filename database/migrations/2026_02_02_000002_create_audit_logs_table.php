<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table des logs d'audit.
 * 
 * Cette table enregistre toutes les actions sensibles effectuées
 * par les superadmins et les propriétaires pour traçabilité complète.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Utilisateur qui a effectué l'action
            $table->enum('user_type', ['superadmin', 'proprio']);
            $table->unsignedBigInteger('user_id');
            
            // Action effectuée
            $table->string('action'); // created, updated, deleted, approved, etc.
            
            // Modèle concerné
            $table->string('model')->nullable(); // Proprio, WifiZone, Transaction, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            
            // Données avant/après modification
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Informations de connexion
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Timestamp unique (pas de updated_at nécessaire pour les logs)
            $table->timestamp('created_at');
            
            // Index pour recherches rapides
            $table->index(['user_type', 'user_id']);
            $table->index('action');
            $table->index(['model', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
