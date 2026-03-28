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
        Schema::table('forfaits', function (Blueprint $table) {
            $table->integer('stock_max')->nullable()->after('description')->comment('Le nombre idéal de tickets à avoir d\'avance');
            $table->integer('seuil_alerte')->default(15)->after('stock_max')->comment('Le pourcentage à partir duquel le système s\'inquiète (ex: 15%)');
            $table->boolean('auto_replenish')->default(false)->after('seuil_alerte')->comment('Activer ou non la génération automatique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forfaits', function (Blueprint $table) {
            $table->dropColumn(['stock_max', 'seuil_alerte', 'auto_replenish']);
        });
    }
};
