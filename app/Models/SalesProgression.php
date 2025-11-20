<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesProgression extends Model
{
    protected $table = 'sales_progression';

    protected $fillable = [
        'property_id',
        'buyer_id',
        'offer_id',
        'solicitor_buyer',
        'solicitor_seller',
        'memorandum_of_sale_issued',
        'enquiries_raised',
        'enquiries_answered',
        'searches_ordered',
        'searches_received',
        'exchange_date',
        'completion_date',
    ];

    protected function casts(): array
    {
        return [
            'memorandum_of_sale_issued' => 'boolean',
            'enquiries_raised' => 'boolean',
            'enquiries_answered' => 'boolean',
            'searches_ordered' => 'boolean',
            'searches_received' => 'boolean',
            'exchange_date' => 'date',
            'completion_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the property for this sales progression.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the buyer for this sales progression.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the offer for this sales progression.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
