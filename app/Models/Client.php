<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle représentant un client utilisateur de la plateforme WiFi.
 */
class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'nom_complet',
        'telephone',
        'mac_address',
        'password',
        'total_depense',
        'derniere_zone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Vérifie si le client est bloqué par un propriétaire spécifique
     * Utiliser cette méthode explicitement dans les contrôleurs
     */
    public function isBlockedBy($proprioId)
    {
        return BlockedClient::isBlocked($this->id, $proprioId);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }
}
