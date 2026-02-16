<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration:
     * - Renomme la table 'withdrawals' en 'retraits'
     * - Ajoute les champs nécessaires pour l'intégration FedaPay Payout API
     * - Les champs existants sont préservés
     */
    public function up(): void
    {
        // Renommer la table withdrawals en retraits
        Schema::rename('withdrawals', 'retraits');

        // Ajouter les nouveaux champs pour FedaPay Payout
        Schema::table('retraits', function (Blueprint $table) {
            // FedaPay Payout ID
            $table->string('fedapay_payout_id')->nullable()->after('reference');
            
            // Mode de paiement (mtn_open, moov_money, etc.)
            $table->string('mode')->nullable()->after('operator');
            
            // Description du payout
            $table->text('description')->nullable()->after('mode');
            
            // Référence marchand (unique)
            $table->string('merchant_reference')->unique()->nullable()->after('description');
            
            // Métadonnées personnalisées (JSON)
            $table->json('custom_metadata')->nullable()->after('merchant_reference');
            
            // Pays du bénéficiaire
            $table->string('country', 2)->default('bj')->after('phone_number');
            
            // Date prévue d'envoi
            $table->timestamp('scheduled_at')->nullable()->after('requested_at');
            
            // Statut FedaPay (pending, started, processing, sent, failed)
            $table->string('fedapay_status')->nullable()->after('status');
            
            // Message d'erreur FedaPay
            $table->text('fedapay_error_message')->nullable()->after('fedapay_status');
            
            // Mettre à jour le statut pour correspondre aux statuts FedaPay
            // Les valeurs existantes 'pending', 'processing', 'completed', 'cancelled', 'failed' sont conservées
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les nouveaux champs
        Schema::table('retraits', function (Blueprint $table) {
            $table->dropColumn([
                'fedapay_payout_id',
                'mode',
                'description',
                'merchant_reference',
                'custom_metadata',
                'country',
                'scheduled_at',
                'fedapay_status',
                'fedapay_error_message',
            ]);
        });

        // Rétablir le nom original de la table
        Schema::rename('retraits', 'withdrawals');
    }
};
