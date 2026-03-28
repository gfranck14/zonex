<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mettre à jour les enregistrements existants qui sont NULL
        DB::table('forfaits')->whereNull('stock_max')->update(['stock_max' => 200]);

        // 2. Changer le défaut de la colonne
        Schema::table('forfaits', function (Blueprint $table) {
            $table->integer('stock_max')->default(200)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forfaits', function (Blueprint $table) {
            $table->integer('stock_max')->nullable()->default(null)->change();
        });
    }
};
