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
        Schema::table('proprio', function (Blueprint $table) {
            $table->string('email')->nullable()->after('prenom');
            $table->string('wa_numero')->nullable()->after('numero');
            $table->boolean('wa_notifications_enabled')->default(true)->after('wa_numero');
            $table->integer('wa_alert_threshold')->default(15)->after('wa_notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proprio', function (Blueprint $table) {
            $table->dropColumn(['email', 'wa_numero', 'wa_notifications_enabled', 'wa_alert_threshold']);
        });
    }
};
