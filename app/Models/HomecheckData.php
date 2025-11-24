<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomecheckData extends Model
{
    protected $table = 'homecheck_data';

    protected $fillable = [
        'property_id',
        'homecheck_report_id',
        'room_name',
        'image_path',
        'is_360',
        'moisture_reading',
        'ai_rating',
        'ai_comments',
    ];

    protected function casts(): array
    {
        return [
            'is_360' => 'boolean',
            'moisture_reading' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the property this homecheck data belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the homecheck report this data belongs to.
     */
    public function homecheckReport(): BelongsTo
    {
        return $this->belongsTo(HomecheckReport::class);
    }
}
