<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomecheckData extends Model
{
    protected $table = 'homecheck_data';

    protected $fillable = [
        'property_id',
        'room_name',
        'image_path',
        'moisture_reading',
        'ai_rating',
        'ai_comments',
    ];

    protected function casts(): array
    {
        return [
            'moisture_reading' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the property this homecheck data belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
