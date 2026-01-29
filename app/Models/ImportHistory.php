<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant l'historique des imports de tickets.
 * 
 * Ce modèle gère les informations sur les imports de tickets CSV effectués par les propriétaires.
 * Il permet de suivre les succès, échecs et observations des imports.
 */
class ImportHistory extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - proprio_id: ID du propriétaire qui a effectué l'import
     * - nom_fichier: Nom du fichier CSV importé
     * - zone_nom: Nom de la zone WiFi concernée
     * - forfait_nom: Nom du forfait associé
     * - quantite: Nombre de tickets importés
     * - statut: Statut de l'import (ex: "success", "partiel", "echec")
     * - observation: Observations sur l'import (ex: "5 tickets importés, 2 doublons")
     */
    protected $fillable = [
        'proprio_id',
        'nom_fichier',
        'zone_nom',
        'forfait_nom',
        'quantite',
        'statut',
        'observation'
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     * 
     * - quantite: Conversion en entier
     * - created_at: Conversion en objet DateTime
     * - updated_at: Conversion en objet DateTime
     */
    protected $casts = [
        'quantite' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec le propriétaire qui a effectué l'import.
     * 
     * Un historique d'import appartient à un seul propriétaire (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proprio()
    {
        return $this->belongsTo(Proprio::class, 'proprio_id');
    }
}
