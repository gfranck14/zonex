<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table des superadmins.
 * 
 * Cette table stocke les comptes administrateurs de la plateforme
 * avec des privilèges élevés (GOD level).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('superadmins', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            // Rôle et permissions
            $table->enum('role', ['god', 'admin', 'support'])->default('admin');
            
            // Authentification à deux facteurs (2FA)
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            
            // Statut et activité
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Index pour performance
            $table->index('email');
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superadmins');
    }
};
