<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle représentant un forfait WiFi.
 * 
 * Ce modèle gère les différents forfaits WiFi disponibles pour les zones.
 * Chaque forfait est associé à une zone WiFi et peut avoir plusieurs tickets.
 */
class Forfait extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table dans la base de données.
     */
    protected $table = 'forfaits';

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - wifizones_id: ID de la zone WiFi associée
     * - nom: Nom du forfait
     * - prix: Prix du forfait
     * - validite: Durée de validité du forfait (ex: "1h", "24h", "7j")
     * - temps_limit: Limite de temps du forfait (ex: "2 heures", "1 jour", "1 mois")
     * - description: Description détaillée du forfait
     * - color_class: Classe CSS pour la couleur du forfait dans l'interface
     */
    protected $fillable = [
        'wifizones_id',
        'nom',
        'prix',
        'validite',
        'temps_limit',
        'description',
        'color_class',
        'is_active',
        'stock_max',
        'seuil_alerte',
        'auto_replenish'
    ];

    /**
     * Relation avec la zone WiFi associée.
     * 
     * Un forfait appartient à une seule zone WiFi (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wifizone()
    {
        return $this->belongsTo(WifiZone::class, 'wifizones_id');
    }

    /**
     * Relation avec les tickets associés au forfait.
     * 
     * Un forfait peut avoir plusieurs tickets (relation one-to-many).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'forfaits_id'); 
    }
}

