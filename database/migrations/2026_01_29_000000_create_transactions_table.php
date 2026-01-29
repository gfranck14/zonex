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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // e.g., TRX-998231
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('wifizone_id')->nullable()->constrained('wifizones')->onDelete('set null');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('set null');
            $table->enum('type', ['purchase', 'deposit', 'refund'])->default('purchase');
            $table->enum('operator', ['mtn', 'moov', 'celtiis', 'cash'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();

            // Indexes for performance
            $table->index('reference');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
