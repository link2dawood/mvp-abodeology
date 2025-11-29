<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeapEventLog extends Model
{
    protected $fillable = [
        'event_type',
        'status',
        'payload',
        'response',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'retry_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get successful events.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get failed events.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get pending events.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get events by type.
     */
    public function scopeOfType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
