<?php

namespace App\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Proprio;

/**
 * Modèle AuditLog
 * 
 * Enregistre toutes les actions sensibles pour traçabilité complète.
 */
class AuditLog extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'audit_logs';

    /**
     * Pas de updated_at pour les logs
     */
    public $timestamps = false;

    /**
     * Champs mass-assignable
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Casts de type
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation polymorphe avec l'utilisateur
     * Note: Comme user_type peut être 'superadmin' ou 'proprio',
     * on gère cela manuellement dans les scopes
     */
    public function user()
    {
        if ($this->user_type === 'superadmin') {
            return $this->belongsTo(SuperAdmin::class, 'user_id');
        } elseif ($this->user_type === 'proprio') {
            return $this->belongsTo(Proprio::class, 'user_id');
        }
        
        return null;
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Filtre par utilisateur
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userType Type d'utilisateur (superadmin|proprio)
     * @param int $userId ID de l'utilisateur
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, string $userType, int $userId)
    {
        return $query->where('user_type', $userType)
                    ->where('user_id', $userId);
    }

    /**
     * Filtre par modèle
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $model Nom du modèle
     * @param int|null $modelId ID du modèle (optionnel)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModel($query, string $model, ?int $modelId = null)
    {
        $query = $query->where('model', $model);
        
        if ($modelId !== null) {
            $query = $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Filtre par action
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action Action effectuée
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    // =========================================================================
    // MÉTHODES STATIQUES
    // =========================================================================

    /**
     * Enregistre une action dans les logs
     * 
     * @param object $user Utilisateur (SuperAdmin ou Proprio)
     * @param string $action Action effectuée
     * @param string|null $model Modèle concerné
     * @param array|null $oldValues Anciennes valeurs
     * @param array|null $newValues Nouvelles valeurs
     * @param int|null $modelId ID du modèle
     * @return self
     */
    public static function logAction(
        object $user,
        string $action,
        ?string $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $modelId = null
    ): self {
        $userType = $user instanceof SuperAdmin ? 'superadmin' : 'proprio';
        
        return self::create([
            'user_type' => $userType,
            'user_id' => $user->id,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Obtient le nom d'affichage de l'utilisateur
     * 
     * @return string
     */
    public function getUserNameAttribute(): string
    {
        $user = $this->user();
        
        if (!$user) {
            return "Utilisateur inconnu";
        }
        
        if ($this->user_type === 'superadmin') {
            return $user->name ?? 'SuperAdmin';
        } else {
            return ($user->prenom ?? '') . ' ' . ($user->nom ?? '');
        }
    }

    /**
     * Obtient un résumé des changements
     * 
     * @return string
     */
    public function getChangesSummaryAttribute(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return $this->action;
        }
        
        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                
                if ($oldValue !== $newValue) {
                    $changes[] = "$key: $oldValue → $newValue";
                }
            }
        }
        
        return empty($changes) ? $this->action : implode(', ', $changes);
    }
}
