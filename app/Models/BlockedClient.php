<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedClient extends Model
{
    use HasFactory;

    protected $table = 'blocked_clients';

    protected $fillable = [
        'proprio_id',
        'client_id',
        'blocked_until',
        'reason'
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
    ];

    public function proprio()
    {
        return $this->belongsTo(Proprio::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Vérifier si le blocage est toujours actif
     */
    public function isActive(): bool
    {
        // Si blocked_until est null, le blocage est permanent
        // Sinon, vérifier si la date n'est pas encore passée
        return is_null($this->blocked_until) || $this->blocked_until->isFuture();
    }

    /**
     * Vérifier si le client est bloqué par un propriétaire spécifique
     * Le blocage est permanent (jusqu'à déblocage manuel)
     */
    public static function isBlocked(int $clientId, int $proprioId): bool
    {
        return self::where('client_id', $clientId)
            ->where('proprio_id', $proprioId)
            ->exists();
    }
}
