<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDocument extends Model
{
    public $timestamps = false; // Using uploaded_at instead

    protected $fillable = [
        'property_id',
        'document_type',
        'file_path',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the property this document belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
