<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Restructure la table retraits pour garder uniquement les champs essentiels:
     * - id
     * - proprio_id (renommé depuis user_id)
     * - reference
     * - fedapay_payout_id
     * - fedapay_fee
     * - ccorp_fee
     * - amount
     * - momo_number
     * - momo_name
     * - status
     * - approved_by
     * - approved_at
     */
    public function up(): void
    {
        // Drop table and recreate with new structure
        Schema::dropIfExists('retraits');

        Schema::create('retraits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprio_id')->constrained('proprio')->onDelete('cascade');
            $table->string('reference', 50)->unique();
            $table->string('fedapay_payout_id', 100)->nullable();
            $table->decimal('fedapay_fee', 10, 2)->default(0);
            $table->decimal('ccorp_fee', 10, 2)->default(0);
            $table->decimal('amount', 10, 2);
            $table->string('momo_number', 20);
            $table->string('momo_name', 100);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('superadmins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retraits');

        Schema::create('retraits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('proprio')->onDelete('cascade');
            $table->string('reference', 50)->unique();
            $table->string('fedapay_payout_id', 100)->nullable();
            $table->decimal('fedapay_fee', 10, 2)->default(0);
            $table->decimal('ccorp_fee', 10, 2)->default(0);
            $table->decimal('amount', 10, 2);
            $table->string('operator', 20)->nullable();
            $table->string('phone_number', 20);
            $table->string('beneficiary_name', 100);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->string('mobile_money_ref', 100)->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('fedapay_status', 50)->nullable();
            $table->string('fedapay_error_message', 255)->nullable();
            $table->timestamps();
        });
    }
};
