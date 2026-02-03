<?php

namespace App\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle SystemConfig
 * 
 * Gère les configurations dynamiques de la plateforme.
 */
class SystemConfig extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'system_configs';

    /**
     * Champs mass-assignable
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'updated_by',
    ];

    /**
     * Casts de type
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation avec le superadmin qui a fait la dernière modification
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'updated_by');
    }

    // =========================================================================
    // MÉTHODES STATIQUES
    // =========================================================================

    /**
     * Récupère une valeur de configuration
     * 
     * @param string $key Clé de configuration
     * @param mixed $default Valeur par défaut si non trouvée
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $config = self::where('key', $key)->first();
        
        if (!$config) {
            return $default;
        }
        
        return self::castValue($config->value, $config->type);
    }

    /**
     * Définit une valeur de configuration
     * 
     * @param string $key Clé de configuration
     * @param mixed $value Valeur
     * @param string|null $description Description
     * @param int|null $updatedBy ID du superadmin
     * @return self
     */
    public static function set(
        string $key,
        mixed $value,
        ?string $description = null,
        ?int $updatedBy = null
    ): self {
        $type = self::detectType($value);
        $valueString = self::valueToString($value, $type);
        
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueString,
                'type' => $type,
                'description' => $description,
                'updated_by' => $updatedBy,
            ]
        );
    }

    /**
     * Récupère toutes les configurations sous forme de tableau
     * 
     * @return array
     */
    public static function getAll(): array
    {
        $configs = self::all();
        $result = [];
        
        foreach ($configs as $config) {
            $result[$config->key] = self::castValue($config->value, $config->type);
        }
        
        return $result;
    }

    // =========================================================================
    // MÉTHODES UTILITAIRES PRIVÉES
    // =========================================================================

    /**
     * Détecte le type d'une valeur
     */
    private static function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } elseif (is_array($value) || is_object($value)) {
            return 'json';
        } else {
            return 'string';
        }
    }

    /**
     * Convertit une valeur en string pour stockage
     */
    private static function valueToString(mixed $value, string $type): string
    {
        return match($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Convertit une valeur string vers son type natif
     */
    private static function castValue(string $value, string $type): mixed
    {
        return match($type) {
            'boolean' => (bool) $value,
            'number' => is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : 0,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Récupère la valeur castée
     */
    public function getValueCastedAttribute(): mixed
    {
        return self::castValue($this->value, $this->type);
    }
}
