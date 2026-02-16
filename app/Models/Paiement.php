<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_id',
        'forfait_id',
        'ticket_id',
        'montant',
        'telephone',
        'reference',
        'statut',
        'methode',
        'devise',
        'fedapay_transaction_id',
        'erreur_message'
    ];
    
    protected $casts = [
        'montant' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtenir le client associé
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Obtenir le forfait associé
     */
    public function forfait()
    {
        return $this->belongsTo(Forfait::class);
    }
    
    /**
     * Obtenir le ticket associé
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Vérifier si le paiement est réussi
     */
    public function estReussi()
    {
        return $this->statut === 'reussi';
    }
    
    /**
     * Vérifier si le paiement est en attente
     */
    public function estEnAttente()
    {
        return $this->statut === 'en_attente';
    }
    
    /**
     * Vérifier si le paiement a échoué
     */
    public function estEchoue()
    {
        return in_array($this->statut, ['echoue', 'annule']);
    }
    
    /**
     * Obtenir le statut formaté pour l'affichage
     */
    public function getStatutFormatteAttribute()
    {
        $statuts = [
            'en_attente' => 'En attente',
            'reussi' => 'Réussi',
            'echoue' => 'Échoué',
            'annule' => 'Annulé'
        ];
        
        return $statuts[$this->statut] ?? 'Inconnu';
    }
}
