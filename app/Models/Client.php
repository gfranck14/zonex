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
        'proprio_id',
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

    /**
     * Scope pour filtrer les clients appartenant à un propriétaire spécifique.
     * Un client "appartient" à un propriétaire s'il a au moins un ticket 
     * dans l'une des zones WiFi du propriétaire.
     */
    public function scopeForProprio($query, $proprioId)
    {
        return $query->where(function($sub) use ($proprioId) {
            // 1. Clients ayant des tickets dans les zones du propriétaire
            $sub->whereHas('tickets', function($q) use ($proprioId) {
                $q->whereHas('forfait', function($qF) use ($proprioId) {
                    $qF->whereHas('wifizone', function($qZ) use ($proprioId) {
                        $qZ->where('proprio_id', $proprioId);
                    });
                });
            })
            // 2. OU clients créés manuellement par ce propriétaire
            ->orWhere('proprio_id', $proprioId);
        });
    }
}
