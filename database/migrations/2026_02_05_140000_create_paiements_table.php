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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('forfait_id')->constrained('forfaits')->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->decimal('montant', 10, 2);
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('reference')->unique();
            $table->enum('statut', ['en_attente', 'reussi', 'echoue', 'annule'])->default('en_attente');
            $table->string('methode')->default('fedapay');
            $table->string('devise')->default('XOF');
            $table->string('fedapay_transaction_id')->nullable()->unique();
            $table->text('fedapay_payment_url')->nullable();
            $table->string('fedapay_status')->nullable();
            $table->string('fedapay_customer_id')->nullable();
            $table->text('erreur_message')->nullable();
            $table->timestamp('date_mise_a_jour')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'statut']);
            $table->index(['forfait_id', 'statut']);
            $table->index('fedapay_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
