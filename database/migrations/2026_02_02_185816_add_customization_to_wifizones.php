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
            $table->string('display_name')->nullable()->after('nom_zone');
            $table->text('welcome_message')->nullable()->after('display_name');
            // Adding primary_color for extra personalization potential
            $table->string('primary_color')->default('#dc2626')->after('welcome_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifizones', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'welcome_message', 'primary_color']);
        });
    }
};
