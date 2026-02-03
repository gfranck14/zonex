<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table des tickets de support.
 * 
 * Système de ticketing pour communication entre propriétaires et superadmins.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            
            // Propriétaire qui a créé le ticket
            $table->unsignedBigInteger('proprio_id');
            $table->foreign('proprio_id')->references('id')->on('proprio')->onDelete('cascade');
            
            // Informations du ticket
            $table->string('subject');
            $table->text('description');
            
            // Statut et priorité
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Assignation
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('superadmins')->onDelete('set null');
            
            // Résolution
            $table->timestamp('resolved_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Index
            $table->index('proprio_id');
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
