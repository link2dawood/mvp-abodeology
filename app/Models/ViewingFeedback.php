<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewingFeedback extends Model
{
    protected $table = 'viewing_feedback';

    protected $fillable = [
        'viewing_id',
        'buyer_interest_level',
        'buyer_interested',
        'buyer_feedback',
        'buyer_questions',
        'property_condition',
        'buyer_notes',
        'pva_notes',
        'buyer_interest', // Legacy field
        'offer_intent', // Legacy field
        'feedback_text', // Legacy field
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the viewing this feedback belongs to.
     */
    public function viewing(): BelongsTo
    {
        return $this->belongsTo(Viewing::class);
    }
}
