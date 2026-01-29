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
        Schema::create('forfaits', function (Blueprint $table) {
            $table->id();
            
            // REVIENT À LA LOGIQUE ZONE : Le forfait appartient à UNE zone spécifique
            $table->foreignId('wifizones_id')->constrained('wifizones')->onDelete('cascade');
            
            $table->string('nom');              // ex: Forfait 1H
            $table->integer('prix');            // ex: 100
            $table->string('validite');         // ex: 1 Heure
            $table->string('profile_mikrotik'); // ex: 1h_limit
            $table->text('description')->nullable(); 
            $table->string('color_class')->default('bg-brand-blue');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forfaits');
    }
};