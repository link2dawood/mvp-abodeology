<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valuation extends Model
{
    protected $fillable = [
        'seller_id',
        'property_address',
        'postcode',
        'property_type',
        'bedrooms',
        'estimated_value',
        'valuation_date',
        'valuation_time',
        'status',
        'notes',
        'seller_notes',
        'id_visual_check',
        'id_visual_check_notes',
    ];

    protected function casts(): array
    {
        return [
            'valuation_date' => 'date',
            'valuation_time' => 'datetime',
            'estimated_value' => 'decimal:2',
            'id_visual_check' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the seller that owns this valuation.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
