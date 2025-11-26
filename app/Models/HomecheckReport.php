<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomecheckReport extends Model
{
    protected $table = 'homecheck_reports';
    
    public $timestamps = true;

    protected $fillable = [
        'property_id',
        'status',
        'scheduled_by',
        'scheduled_date',
        'completed_by',
        'completed_at',
        'provider',
        'report_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the property this report belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who scheduled this HomeCheck.
     */
    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    /**
     * Get the user who completed this HomeCheck.
     */
    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the homecheck data (images/photos) for this report.
     */
    public function homecheckData()
    {
        return $this->hasMany(HomecheckData::class, 'homecheck_report_id');
    }
}
