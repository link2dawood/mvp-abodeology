<?php

namespace App\Models;

use App\Traits\Ownable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Viewing extends Model
{
    use Ownable;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'pva_id',
        'viewing_date',
        'scheduled_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'viewing_date' => 'datetime',
            'scheduled_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the owner key for this model.
     * Viewings have multiple potential owners:
     * - Buyer owns their viewings
     * - Seller owns viewings for their properties
     * - PVA owns viewings they're assigned to
     */
    protected function getOwnerKey(): string
    {
        return 'buyer_id'; // Default to buyer, but check via canBeViewedBy
    }

    /**
     * Check if user can view this viewing.
     * Buyers can see their own viewings, sellers can see viewings for their properties,
     * PVAs can see viewings they're assigned to.
     */
    public function canBeViewedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin and agent can see all viewings
        if (in_array($user->role, ['admin', 'agent'])) {
            return true;
        }

        // Buyer can see their own viewings
        if ($user->role === 'buyer' && $this->buyer_id === $user->id) {
            return true;
        }

        // Seller can see viewings for their properties
        if (in_array($user->role, ['seller', 'both'])) {
            // Load property if not already loaded
            if (!$this->relationLoaded('property')) {
                $this->load('property');
            }
            
            if ($this->property && $this->property->seller_id === $user->id) {
                return true;
            }
        }

        // PVA can see viewings they're assigned to
        if ($user->role === 'pva' && $this->pva_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Get the property for this viewing.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the buyer for this viewing.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the PVA assigned to this viewing.
     */
    public function pva(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pva_id');
    }

    /**
     * Get feedback for this viewing.
     */
    public function feedback()
    {
        return $this->hasOne(ViewingFeedback::class);
    }
}
