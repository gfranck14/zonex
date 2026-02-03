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
        'password', // Added
        'total_depense',
        'derniere_zone',
        'is_blocked'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }
}
