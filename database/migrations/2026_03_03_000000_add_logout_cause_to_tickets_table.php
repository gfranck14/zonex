<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajoute le champ logout_cause pour suivre les causes de déconnexion Mikrotik
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('logout_cause')->nullable()->after('prix_achat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('logout_cause');
        });
    }
};
