<?php

namespace App\Models;

use App\Traits\Ownable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use Ownable;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'offer_amount',
        'deposit_amount',
        'funding_type',
        'aip_status',
        'chain_position',
        'conditions',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'offer_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the owner key for this model.
     * For offers, ownership depends on context:
     * - Buyer owns the offer (they created it)
     * - Seller owns the property the offer is for
     */
    protected function getOwnerKey(): string
    {
        return 'buyer_id';
    }

    /**
     * Check if user can view this offer.
     * Buyers can see their own offers, sellers can see offers on their properties.
     */
    public function canBeViewedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin and agent can see all offers
        if (in_array($user->role, ['admin', 'agent'])) {
            return true;
        }

        // Buyer can see their own offers
        if ($user->role === 'buyer' && $this->buyer_id === $user->id) {
            return true;
        }

        // Seller can see offers on their properties
        if (in_array($user->role, ['seller', 'both'])) {
            // Load property if not already loaded
            if (!$this->relationLoaded('property')) {
                $this->load('property');
            }
            
            if ($this->property && $this->property->seller_id === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the property this offer is for.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the buyer who made this offer.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get offer decisions for this offer.
     */
    public function decisions()
    {
        return $this->hasMany(OfferDecision::class);
    }

    /**
     * Get the latest decision for this offer.
     */
    public function latestDecision()
    {
        return $this->hasOne(OfferDecision::class)->latestOfMany('decided_at');
    }
}
