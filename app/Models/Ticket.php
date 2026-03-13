<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant un ticket WiFi.
 * 
 * Ce modèle gère les tickets WiFi disponibles, vendus ou utilisés.
 * Chaque ticket est associé à un forfait et peut être associé à un client.
 */
class Ticket extends Model
{
    use HasFactory;

    /**
     * Nom de la table dans la base de données.
     */
    protected $table = 'tickets';

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - forfaits_id: ID du forfait associé au ticket
     * - username: Nom d'utilisateur pour accéder au WiFi
     * - password: Mot de passe pour accéder au WiFi
     * - statut: Statut du ticket (libre, vendu, utilisé)
     * - client_id: ID du client qui a acheté le ticket
     * - date_vente: Date de vente du ticket
     */
    protected $fillable = [
        'forfaits_id',
        'username',
        'password',
        'statut',
        'client_id',
        'date_vente',
        'import_batch_id',
        'prix_achat',
        'last_ip',
        'last_mac',
        'statut_connexion',
        'logout_cause',
        'derniere_connexion',
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     * 
     * - date_vente: Conversion automatique en objet DateTime
     */
    protected $casts = [
        'date_vente' => 'datetime',
        'derniere_connexion' => 'datetime',
    ];

    /**
     * Relation avec le forfait associé au ticket.
     * 
     * Un ticket appartient à un seul forfait (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function forfait()
    {
        return $this->belongsTo(Forfait::class, 'forfaits_id');
    }

    /**
     * Relation avec le client qui a acheté le ticket.
     * 
     * Un ticket peut être associé à un seul client (relation many-to-one).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
