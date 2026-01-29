<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant une transaction.
 * 
 * Ce modèle gère les transactions effectuées par les clients pour l'achat de tickets WiFi.
 * Chaque transaction est associée à un client, une zone WiFi et un ticket.
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - reference: Référence unique de la transaction
     * - client_id: ID du client qui a effectué la transaction
     * - wifizone_id: ID de la zone WiFi concernée
     * - ticket_id: ID du ticket acheté
     * - type: Type de transaction (ex: "achat", "retrait")
     * - operator: Opérateur mobile money utilisé (ex: "mtn", "moov", "celtiis")
     * - amount: Montant de la transaction
     * - status: Statut de la transaction (ex: "pending", "success", "failed")
     */
    protected $fillable = [
        'reference',
        'client_id',
        'wifizone_id',
        'ticket_id',
        'type',
        'operator',
        'amount',
        'status',
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     * 
     * - amount: Conversion en décimal avec 2 décimales
     * - created_at: Conversion en objet DateTime
     * - updated_at: Conversion en objet DateTime
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    /**
     * Relation avec le client qui a effectué la transaction.
     * 
     * Une transaction appartient à un seul client (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec la zone WiFi concernée par la transaction.
     * 
     * Une transaction est associée à une seule zone WiFi (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wifizone()
    {
        return $this->belongsTo(Wifizone::class);
    }

    /**
     * Relation avec le ticket acheté.
     * 
     * Une transaction est associée à un seul ticket (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // -------------------------------------------------------------------------
    // SCOPES DE RECHERCHE
    // -------------------------------------------------------------------------

    /**
     * Scope pour filtrer les transactions par statut.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer les transactions par opérateur mobile money.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $operator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOperator($query, $operator)
    {
        return $query->where('operator', $operator);
    }

    /**
     * Scope pour filtrer les transactions par type.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer les transactions par zone WiFi.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $zoneId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByZone($query, $zoneId)
    {
        return $query->where('wifizone_id', $zoneId);
    }

    /**
     * Scope pour filtrer les transactions réussies.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    // -------------------------------------------------------------------------
    // METHODES UTILITAIRES
    // -------------------------------------------------------------------------

    /**
     * Génère une référence unique pour une transaction.
     * 
     * Format: TRX-XXXXXX où XXXXXX est un numéro séquentiel à 6 chiffres.
     * 
     * @return string Référence unique
     */
    public static function generateReference()
    {
        $lastTransaction = self::latest('id')->first();
        $number = $lastTransaction ? $lastTransaction->id + 1 : 1;
        return 'TRX-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Marque la transaction comme réussie.
     */
    public function markAsSuccess()
    {
        $this->update(['status' => 'success']);
    }

    /**
     * Marque la transaction comme échouée.
     */
    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
}
