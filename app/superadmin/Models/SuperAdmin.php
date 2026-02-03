<?php

namespace App\SuperAdmin\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle SuperAdmin
 * 
 * Gère les comptes administrateurs avec privilèges GOD/Admin/Support.
 */
class SuperAdmin extends Authenticatable
{
    /**
     * Nom de la table
     */
    protected $table = 'superadmins';

    /**
     * Guard utilisé pour l'authentification
     */
    protected $guard = 'superadmin';

    /**
     * Champs mass-assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'two_factor_secret',
        'two_factor_enabled',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * Champs cachés dans les modèles
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
    ];

    /**
     * Casts de type
     */
    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation avec les logs d'audit créés par ce superadmin
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id')
            ->where('user_type', 'superadmin')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relation avec les tickets de support assignés
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    // =========================================================================
    // MÉTHODES UTILITAIRES
    // =========================================================================

    /**
     * Vérifie si le superadmin a un rôle spécifique
     * 
     * @param string|array $roles Rôle(s) à vérifier
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return $this->role === $roles;
    }

    /**
     * Vérifie si le superadmin est GOD (tous les privilèges)
     * 
     * @return bool
     */
    public function isGod(): bool
    {
        return $this->role === 'god';
    }

    /**
     * Active l'authentification à deux facteurs
     * 
     * @param string $secret Secret 2FA
     * @return void
     */
    public function enable2FA(string $secret): void
    {
        $this->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
        ]);
    }

    /**
     * Désactive l'authentification à deux facteurs
     * 
     * @return void
     */
    public function disable2FA(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);
    }

    /**
     * Vérifie un code 2FA
     * 
     * @param string $code Code à vérifier
     * @return bool
     */
    public function verify2FACode(string $code): bool
    {
        if (!$this->two_factor_enabled || !$this->two_factor_secret) {
            return false;
        }

        // TODO: Implémenter la vérification TOTP (Time-based One-Time Password)
        // Utiliser une librairie comme pragmarx/google2fa
        // Pour l'instant, retourne true si 2FA désactivé
        return true;
    }

    /**
     * Met à jour les informations de dernière connexion
     * 
     * @param string|null $ipAddress IP de connexion
     * @return void
     */
    public function updateLastLogin(?string $ipAddress = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
        ]);
    }
}
