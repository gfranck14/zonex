<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class SuperAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nom de la table
     */
    protected $table = 'superadmins';

    /**
     * Les attributs assignables en masse
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'two_factor_enabled',
        'two_factor_type',
        'two_factor_secret',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * Les attributs à cacher
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Les attributs à caster
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * Constantes de rôles
     */
    public const ROLE_GOD = 'god';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MODERATOR = 'moderator';

    /**
     * Types 2FA
     */
    public const TWO_FACTOR_TOTP = 'totp';
    public const TWO_FACTOR_EMAIL = 'email';
    public const TWO_FACTOR_NONE = null;

    /**
     * Vérifie si l'utilisateur est un "god"
     */
    public function isGod(): bool
    {
        return $this->role === self::ROLE_GOD;
    }

    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_GOD, self::ROLE_ADMIN]);
    }

    /**
     * Vérifie si l'utilisateur a accès aux settings
     */
    public function canAccessSettings(): bool
    {
        return $this->isGod();
    }

    /**
     * Définit le mot de passe
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Vérifie le mot de passe
     */
    public function verifyPassword($password): bool
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Génère une clé secrète TOTP
     */
    public function generateTotpSecret(): string
    {
        $google2fa = new Google2FA();
        return $google2fa->generateSecretKey(32);
    }

    /**
     * Vérifie le code TOTP
     */
    public function verifyTotpCode(string $code): bool
    {
        $google2fa = new Google2FA();
        return $google2fa->verifyKey($this->two_factor_secret, $code);
    }

    /**
     * Génère le QR code pour TOTP
     */
    public function getTotpQrCodeUrl(): string
    {
        $google2fa = new Google2FA();
        return $google2fa->getQRCodeUrl(
            config('app.name') . ' SuperAdmin',
            $this->email,
            $this->two_factor_secret
        );
    }

    /**
     * Vérifie le code email (simplifié)
     */
    public function verifyEmailCode(string $code): bool
    {
        return cache()->has('2fa_email_' . $this->id . '_' . $code);
    }

    /**
     * Envoie un code par email
     */
    public function sendEmailCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        cache()->put('2fa_email_' . $this->id . '_' . $code, true, 600);
        return $code;
    }

    /**
     * Enregistre la connexion
     */
    public function recordLogin($request): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);
    }
}
