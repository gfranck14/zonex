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
        Schema::create('blocked_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proprio_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamp('blocked_until')->nullable(); // NULL = blocage permanent, sinon temporaire
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->foreign('proprio_id')->references('id')->on('proprio')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unique(['proprio_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_clients');
    }
};
