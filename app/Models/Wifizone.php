<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiZone extends Model
{
    use HasFactory;

    protected $table = 'wifizones';

    // Champs remplissables
    protected $fillable = [
        'proprio_id',
        'nom_zone',
        'adresse',
        'token',
    ];

    /**
     * Relation : une zone WiFi appartient à un proprio
     */
    public function proprio()
    {
        return $this->belongsTo(Proprio::class, 'proprio_id');
    }

    /**
     * Relation : une zone WiFi peut avoir plusieurs forfaits
     */
    public function forfaits()
    {
        return $this->hasMany(Forfait::class, 'wifizones_id');
    }
}
