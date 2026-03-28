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
            $table->renameColumn('profil_mikrotik', 'temps_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forfaits', function (Blueprint $table) {
            $table->renameColumn('temps_limit', 'profil_mikrotik');
        });
    }
};
