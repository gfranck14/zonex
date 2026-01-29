<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Modèle représentant un propriétaire de zone WiFi.
 * 
 * Ce modèle gère les informations des utilisateurs de type "propriétaire"
 * qui peuvent créer et gérer des zones WiFi, des forfaits et des tickets.
 */
class Proprio extends Authenticatable
{
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
}
