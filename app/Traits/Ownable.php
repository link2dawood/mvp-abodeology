<?php

namespace App\Traits;

use App\Models\User;

trait Ownable
{
    /**
     * Check if the model is owned by a given user.
     *
     * @param User|null $user
     * @return bool
     */
    public function isOwnedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin and agent roles can access all resources
        if (in_array($user->role, ['admin', 'agent'])) {
            return true;
        }

        // Check ownership based on the owner relationship
        $ownerKey = $this->getOwnerKey();
        return $this->{$ownerKey} === $user->id;
    }

    /**
     * Get the owner key name (e.g., 'user_id', 'seller_id', 'buyer_id').
     *
     * @return string
     */
    protected function getOwnerKey(): string
    {
        // Try common owner key names
        $possibleKeys = ['user_id', 'seller_id', 'buyer_id', 'created_by', 'owner_id'];

        foreach ($possibleKeys as $key) {
            if (isset($this->attributes[$key]) || $this->relationLoaded($key)) {
                return $key;
            }
        }

        // Default to 'user_id'
        return 'user_id';
    }

    /**
     * Scope a query to only include resources owned by the given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnedBy($query, User $user)
    {
        // Admin and agent can see all resources
        if (in_array($user->role, ['admin', 'agent'])) {
            return $query;
        }

        $ownerKey = $this->getOwnerKey();
        return $query->where($ownerKey, $user->id);
    }

    /**
     * Get the owner relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        $ownerKey = $this->getOwnerKey();
        $relationName = str_replace('_id', '', $ownerKey);
        
        // Map common patterns
        $relationMap = [
            'user_id' => 'user',
            'seller_id' => 'seller',
            'buyer_id' => 'buyer',
        ];

        $relation = $relationMap[$ownerKey] ?? $relationName;
        
        return $this->belongsTo(User::class, $ownerKey, 'id');
    }
}

