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
        Schema::table('wifizones', function (Blueprint $table) {
            $table->string('api_host')->nullable()->comment('ex: 192.168.88.1 ou xyz.sn.mynetname.net');
            $table->integer('api_port')->default(8728);
            $table->string('api_user')->nullable()->comment('Utilisateur API spécifique');
            $table->text('api_password')->nullable()->comment('Mot de passe chiffré');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifizones', function (Blueprint $table) {
            $table->dropColumn(['api_host', 'api_port', 'api_user', 'api_password']);
        });
    }
};
