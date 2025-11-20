<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewingFeedback extends Model
{
    protected $table = 'viewing_feedback';

    protected $fillable = [
        'viewing_id',
        'buyer_interest',
        'offer_intent',
        'feedback_text',
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
