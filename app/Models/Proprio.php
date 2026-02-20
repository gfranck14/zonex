<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Paiement;
use App\Models\Retrait;

/**
 * Modèle représentant un propriétaire de zone WiFi.
 * 
 * Ce modèle gère les informations des utilisateurs de type "propriétaire"
 * qui peuvent créer et gérer des zones WiFi, des forfaits et des tickets.
 */
class Proprio extends Authenticatable
{
    use HasFactory;

    /**
     * Nom de la table dans la base de données.
     */
    protected $table = 'proprio';

    /**
     * Colonnes autorisées pour l'assignment en masse (mass assignment).
     * 
     * - nom: Nom de famille du propriétaire
     * - prenom: Prénom du propriétaire
     * - email: Adresse email (optionnelle)
     * - numero: Numéro de téléphone complet (code pays + numéro)
     * - wa_numero: Numéro WhatsApp complet (code pays + numéro)
     * - wa_notifications_enabled: Activation des notifications WhatsApp
     * - wa_alert_threshold: Seuil de stock minimum pour les notifications WhatsApp
     * - password: Mot de passe (haché)
     * - is_active: Statut du compte (true = actif)
     * - deactivation_reason: Raison de désactivation du compte (si applicable)
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'numero',
        'wa_numero',
        'wa_notifications_enabled',
        'wa_alert_threshold',
        'password',
        'password_reset_token',
        'password_reset_expires_at',
        'is_active',
        'deactivation_reason'
    ];

    /**
     * Colonnes à masquer lors de la conversion en tableau ou JSON.
     * 
     * - password: Mot de passe (pour des raisons de sécurité)
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Relation avec les zones WiFi.
     * 
     * Un propriétaire peut avoir plusieurs zones WiFi (relation one-to-many).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wifizones()
    {
        return $this->hasMany(WifiZone::class, 'proprio_id');
    }

    /**
     * Relation avec les transactions.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'proprio_id');
    }

    /**
     * Calcule le solde du propriétaire.
     * 
     * Formule: Total des paiements réussis (via forfaits/zones) - Total des retraits
     * Utilise la même logique que PaiementController::calculateBalance()
     * 
     * @return int
     */
    public function getBalance()
    {
        $zoneIds = $this->wifizones->pluck('id');
        
        if ($zoneIds->isEmpty()) {
            return 0;
        }
        
        // Total des paiements réussis (statut='reussi')
        // Via: Paiement -> Forfait -> WifiZone
        $totalPaiements = Paiement::whereHas('forfait.wifizone', function($q) use ($zoneIds) {
                $q->whereIn('id', $zoneIds);
            })
            ->where('statut', 'reussi')
            ->sum('montant');
        
        // Total des retraits (status='completed' ou 'processing')
        $totalRetraits = Retrait::where('proprio_id', $this->id)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('amount');
        
        return (int) ($totalPaiements - $totalRetraits);
    }
}
