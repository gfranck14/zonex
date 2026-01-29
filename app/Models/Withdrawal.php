<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant une demande de retrait de fonds.
 * 
 * Ce modèle gère les demandes de retrait des propriétaires de zones WiFi.
 * Les retraits sont associés à un propriétaire et peuvent passer par différents statuts.
 */
class Withdrawal extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - reference: Référence unique de la demande de retrait
     * - proprio_id: ID du propriétaire qui demande le retrait
     * - amount: Montant à retirer
     * - operator: Opérateur mobile money utilisé (MTN, Moov, Celtiis)
     * - phone_number: Numéro de téléphone du bénéficiaire
     * - beneficiary_name: Nom du bénéficiaire
     * - status: Statut de la demande (pending, processing, completed, cancelled, failed)
     * - mobile_money_ref: Référence de la transaction mobile money
     * - requested_at: Date de demande du retrait
     * - processed_at: Date de traitement de la demande
     */
    protected $fillable = [
        'reference',
        'proprio_id',
        'amount',
        'operator',
        'phone_number',
        'beneficiary_name',
        'status',
        'mobile_money_ref',
        'requested_at',
        'processed_at',
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     * 
     * - amount: Conversion en décimal avec 2 décimales
     * - requested_at: Conversion en objet DateTime
     * - processed_at: Conversion en objet DateTime
     * - created_at: Conversion en objet DateTime
     * - updated_at: Conversion en objet DateTime
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    /**
     * Relation avec le propriétaire de la zone WiFi.
     * 
     * Une demande de retrait appartient à un seul propriétaire (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proprio()
    {
        return $this->belongsTo(Proprio::class);
    }

    // -------------------------------------------------------------------------
    // SCOPES DE RECHERCHE
    // -------------------------------------------------------------------------

    /**
     * Scope pour filtrer les demandes en attente.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour filtrer les demandes en cours de traitement.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope pour filtrer les demandes complétées avec succès.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour filtrer les demandes annulées.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // -------------------------------------------------------------------------
    // METHODES UTILITAIRES
    // -------------------------------------------------------------------------

    /**
     * Génère une référence unique pour une demande de retrait.
     * 
     * Format: WDR-YYYY-XXX où XXX est un numéro séquentiel par année.
     * 
     * @return string Référence unique
     */
    public static function generateReference()
    {
        $year = date('Y');
        $lastWithdrawal = self::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastWithdrawal ? (int) substr($lastWithdrawal->reference, -3) + 1 : 1;
        return 'WDR-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Annule une demande de retrait.
     * 
     * Seules les demandes en attente ou en cours de traitement peuvent être annulées.
     * 
     * @return bool true si l'annulation a réussi, false sinon
     */
    public function cancel()
    {
        if ($this->status !== 'pending' && $this->status !== 'processing') {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'processed_at' => now(),
        ]);

        return true;
    }

    /**
     * Marque la demande comme traitée avec succès.
     * 
     * @param string|null $mobileMoneyRef Référence de la transaction mobile money (optionnelle)
     */
    public function markAsProcessed($mobileMoneyRef = null)
    {
        $this->update([
            'status' => 'completed',
            'mobile_money_ref' => $mobileMoneyRef,
            'processed_at' => now(),
        ]);
    }

    /**
     * Marque la demande comme en cours de traitement.
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Marque la demande comme échouée.
     */
    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Vérifie si la demande est en attente.
     * 
     * @return bool true si la demande est en attente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si la demande a été complétée.
     * 
     * @return bool true si la demande est complétée
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si la demande peut être annulée.
     * 
     * @return bool true si la demande est annulable
     */
    public function isCancellable()
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
