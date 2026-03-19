<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientSession extends Model
{
    use HasFactory;

    protected $table = 'client_sessions';

    protected $fillable = [
        'client_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Vérifier si la session est expirée
     */
    public function isExpired($timeoutMinutes = 30)
    {
        return $this->last_activity->lt(now()->subMinutes($timeoutMinutes));
    }
}
