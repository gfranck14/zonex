<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant une opération de retrait.
 * 
 * Ce modèle gère les demandes de retrait des propriétaires de zones WiFi.
 */
class Retrait extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'retraits';

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     */
    protected $fillable = [
        'proprio_id',
        'reference',
        'fedapay_payout_id',
        'fedapay_fee',
        'ccorp_fee',
        'amount',
        'momo_number',
        'momo_name',
        'status',
        'mobile_money_ref',
        'approved_by',
        'approved_at',
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fedapay_fee' => 'decimal:2',
        'ccorp_fee' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Attributs par défaut.
     */
    protected $attributes = [
        'status' => 'pending',
        'fedapay_fee' => 0,
        'ccorp_fee' => 0,
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    /**
     * Relation avec le propriétaire de la zone WiFi.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proprio()
    {
        return $this->belongsTo(Proprio::class, 'proprio_id');
    }

    /**
     * Relation avec le SuperAdmin qui a approuvé.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(\App\Models\SuperAdmin::class, 'approved_by');
    }

    // -------------------------------------------------------------------------
    // SCOPES DE RECHERCHE
    // -------------------------------------------------------------------------

    /**
     * Scope pour filtrer les demandes en attente.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour filtrer les demandes en cours de traitement.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope pour filtrer les demandes complétées avec succès.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour filtrer les demandes annulées.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope pour filtrer les demandes échouées.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour filtrer par propriétaire.
     */
    public function scopeForProprio($query, $proprioId)
    {
        return $query->where('proprio_id', $proprioId);
    }

    // -------------------------------------------------------------------------
    // METHODES UTILITAIRES
    // -------------------------------------------------------------------------

    /**
     * Génère une référence unique pour une demande de retrait.
     * 
     * Format: RET-YYYY-XXX où XXX est un numéro séquentiel par année.
     * 
     * @return string Référence unique
     */
    public static function generateReference()
    {
        $year = date('Y');
        $lastRetrait = self::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastRetrait ? (int) substr($lastRetrait->reference, -3) + 1 : 1;
        return 'RET-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calcule le montant net après déduction des frais.
     * 
     * @return float Montant net
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fedapay_fee - $this->ccorp_fee;
    }

    /**
     * Vérifie si la demande est en attente.
     * 
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si la demande a été complétée.
     * 
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si la demande peut être annulée.
     * 
     * @return bool
     */
    public function isCancellable()
    {
        return in_array($this->status, ['pending']);
    }

    /**
     * Annule la demande de retrait.
     * 
     * @return bool
     */
    public function cancel()
    {
        if ($this->isCancellable()) {
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }

    /**
     * Marque la demande comme étant en cours de traitement.
     * 
     * @return bool
     */
    public function markAsProcessing()
    {
        $this->status = 'processing';
        return $this->save();
    }

    /**
     * Marque la demande comme complétée.
     * 
     * @param string|null $mobile_money_ref
     * @return bool
     */
    public function markAsProcessed($mobile_money_ref = null)
    {
        $this->status = 'completed';
        if ($mobile_money_ref !== null) {
            $this->mobile_money_ref = $mobile_money_ref;
        }
        $this->approved_at = now();
        $this->approved_by = auth('admin')->id() ?? auth('web')->id() ?? null;
        return $this->save();
    }

    /**
     * Marque la demande comme échouée.
     * 
     * @return bool
     */
    public function markAsFailed()
    {
        $this->status = 'failed';
        return $this->save();
    }

    /**
     * Obtient le libellé du statut formaté pour l'affichage.
     * 
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'failed' => 'Échoué',
        ];

        return $labels[$this->status] ?? 'Inconnu';
    }

    /**
     * Obtient le montant formaté pour l'affichage.
     * 
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' F';
    }
}
