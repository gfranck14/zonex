<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forfait extends Model
{
    use HasFactory;

    // Champs remplissables
    protected $fillable = [
        'wifizones_id',  // clé étrangère vers WifiZone
        'profile_name',   // nom du forfait (correspond à la migration)
        'time_limit',    // durée en minutes
        'validite',      // validité en heures
        'prix_vente',    // prix du forfait (correspond à la migration)
    ];

    /**
     * Relation : un forfait appartient à une zone WiFi
     */
    public function wifiZone()
    {
        return $this->belongsTo(WifiZone::class, 'wifizones_id');
    }

    /**
     * Relation : un forfait peut avoir plusieurs tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

