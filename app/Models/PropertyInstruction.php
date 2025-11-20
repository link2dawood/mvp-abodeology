<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInstruction extends Model
{
    protected $fillable = [
        'property_id',
        'seller_id',
        'fee_percentage',
        'declaration_accurate',
        'declaration_legal_entitlement',
        'declaration_immediate_marketing',
        'declaration_terms',
        'declaration_homecheck',
        'seller1_name',
        'seller1_signature',
        'seller1_date',
        'seller2_name',
        'seller2_signature',
        'seller2_date',
        'status',
        'requested_by',
        'requested_at',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'declaration_accurate' => 'boolean',
            'declaration_legal_entitlement' => 'boolean',
            'declaration_immediate_marketing' => 'boolean',
            'declaration_terms' => 'boolean',
            'declaration_homecheck' => 'boolean',
            'seller1_date' => 'date',
            'seller2_date' => 'date',
            'fee_percentage' => 'decimal:2',
            'requested_at' => 'datetime',
            'signed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the property this instruction belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the seller this instruction belongs to.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the agent who requested this instruction.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
