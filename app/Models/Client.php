<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant un client utilisateur de la plateforme WiFi.
 * 
 * Ce modèle gère les informations des clients qui achètent et utilisent des tickets WiFi.
 * Chaque client peut avoir plusieurs tickets associés et peut être bloqué/débloqué.
 */
class Client extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - nom_complet: Nom complet du client
     * - telephone: Numéro de téléphone complet (code pays inclus)
     * - mac_address: Adresse MAC du dispositif WiFi du client
     * - total_depense: Montant total dépensé par le client en tickets
     * - derniere_zone: Dernière zone WiFi utilisée par le client
     * - is_blocked: Statut de blocage du client (true = bloqué)
     */
    protected $fillable = [
        'nom_complet',
        'telephone',
        'mac_address',
        'total_depense',
        'derniere_zone',
        'is_blocked'
    ];

    /**
     * Conversion des attributs vers des types PHP spécifiques.
     * 
     * - date_naissance: Conversion automatique en objet Date
     */
    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation avec les tickets du client.
     * 
     * Un client peut avoir plusieurs tickets (relation one-to-many).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }
}
