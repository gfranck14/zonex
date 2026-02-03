<?php

namespace App\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Proprio;

/**
 * Modèle SupportTicket
 * 
 * Gère les tickets de support entre propriétaires et superadmins.
 */
class SupportTicket extends Model
{
    /**
     * Nom de la table
     */
    protected $table = 'support_tickets';

    /**
     * Champs mass-assignable
     */
    protected $fillable = [
        'proprio_id',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
    ];

    /**
     * Casts de type
     */
    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relation avec le propriétaire qui a créé le ticket
     */
    public function proprio(): BelongsTo
    {
        return $this->belongsTo(Proprio::class, 'proprio_id');
    }

    /**
     * Relation avec le superadmin assigné
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'assigned_to');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Filtre par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filtre par priorité
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Tickets ouverts (open + in_progress)
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Tickets assignés à un superadmin
     */
    public function scopeAssignedTo($query, int $superadminId)
    {
        return $query->where('assigned_to', $superadminId);
    }

    // =========================================================================
    // MÉTHODES
    // =========================================================================

    /**
     * Assigne le ticket à un superadmin
     */
    public function assign(int $superadminId): void
    {
        $this->update([
            'assigned_to' => $superadminId,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Marque le ticket comme résolu
     */
    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Ferme le ticket
     */
    public function close(): void
    {
        $this->update([
            'status' => 'closed',
        ]);
    }

    /**
     * Rouvre le ticket
     */
    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
        ]);
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Badge de statut avec couleur
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'open' => ['label' => 'Ouvert', 'color' => 'blue'],
            'in_progress' => ['label' => 'En cours', 'color' => 'yellow'],
            'resolved' => ['label' => 'Résolu', 'color' => 'green'],
            'closed' => ['label' => 'Fermé', 'color' => 'gray'],
            default => ['label' => $this->status, 'color' => 'gray'],
        };
    }

    /**
     * Badge de priorité avec couleur
     */
    public function getPriorityBadgeAttribute(): array
    {
        return match($this->priority) {
            'urgent' => ['label' => 'Urgent', 'color' => 'red'],
            'high' => ['label' => 'Haute', 'color' => 'orange'],
            'medium' => ['label' => 'Moyenne', 'color' => 'yellow'],
            'low' => ['label' => 'Basse', 'color' => 'green'],
            default => ['label' => $this->priority, 'color' => 'gray'],
        };
    }
}
