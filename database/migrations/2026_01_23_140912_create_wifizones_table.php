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
        Schema::create('wifizones', function (Blueprint $table) { 
    $table->id();
    $table->foreignId('proprio_id')->constrained('proprio')->cascadeOnDelete(); 
    $table->string('nom_zone');
    $table->string('adresse')->nullable();
    $table->string('token')->unique();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifizones');
    }
};
