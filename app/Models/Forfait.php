<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forfait extends Model
{
    use HasFactory;

    // Champs remplissables
    protected $fillable = [
        'wifi_zone_id',  // clé étrangère vers WifiZone
        'nom',           // nom du forfait
        'time_limit',    // durée en minutes (ou selon ton besoin)
        'validite',      // validité en heures
        'prix',          // prix du forfait
    ];

    /**
     * Relation : un forfait appartient à une zone WiFi
     */
    public function wifiZone()
    {
        return $this->belongsTo(WifiZone::class, 'wifi_zone_id');
    }

    /**
     * Relation : un forfait peut avoir plusieurs tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

