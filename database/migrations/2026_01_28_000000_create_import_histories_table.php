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
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprio_id')->constrained('proprio')->onDelete('cascade');
            $table->string('nom_fichier'); // ex: tickets_janvier.csv
            $table->string('zone_nom');    // ex: Bar Central (snapshot pour historique)
            $table->string('forfait_nom'); // ex: 1H (snapshot)
            $table->integer('quantite');   // ex: 50
            $table->string('statut');      // 'succes', 'partiel', 'echec'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_histories');
    }
};
