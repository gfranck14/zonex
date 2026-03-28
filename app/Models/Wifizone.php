<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle représentant une zone WiFi.
 * 
 * Ce modèle gère les zones WiFi physiques créées par les propriétaires.
 * Chaque zone WiFi peut avoir plusieurs forfaits et transactions associées.
 */
class WifiZone extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table dans la base de données.
     */
    protected $table = 'wifizones';

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - proprio_id: ID du propriétaire de la zone
     * - nom_zone: Nom de la zone WiFi
     * - adresse: Adresse physique de la zone
     * - token: Token d'identification pour le portail captif
     */
    protected $fillable = [
        'proprio_id',
        'nom_zone',
        'adresse',
        'hotspot_address',
        'token',
        'display_name',
        'welcome_message',
        'primary_color',
        'ticket_admin_username',
        'ticket_admin_password',
        'api_host',
        'api_port',
        'api_user',
        'api_password',
    ];

    /**
     * Relation avec le propriétaire de la zone WiFi.
     * 
     * Une zone WiFi appartient à un seul propriétaire (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proprio()
    {
        return $this->belongsTo(Proprio::class, 'proprio_id');
    }

    /**
     * Relation avec les forfaits associés à la zone WiFi.
     * 
     * Une zone WiFi peut avoir plusieurs forfaits (relation one-to-many).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forfaits()
    {
        return $this->hasMany(Forfait::class, 'wifizones_id');
    }
}
