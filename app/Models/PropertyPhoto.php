<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPhoto extends Model
{
    public $timestamps = false; // Using uploaded_at instead

    protected $fillable = [
        'property_id',
        'file_path',
        'sort_order',
        'is_primary',
        'caption',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the property this photo belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
