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
            $table->string('ticket_admin_username')->nullable()->after('welcome_message');
            $table->string('ticket_admin_password')->nullable()->after('ticket_admin_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifizones', function (Blueprint $table) {
            $table->dropColumn(['ticket_admin_username', 'ticket_admin_password']);
        });
    }
};
