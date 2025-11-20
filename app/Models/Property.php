<?php

namespace App\Models;

use App\Traits\Ownable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use Ownable;

    protected $fillable = [
        'seller_id',
        'address',
        'postcode',
        'property_type',
        'bedrooms',
        'bathrooms',
        'parking',
        'tenure',
        'lease_years_remaining',
        'ground_rent',
        'service_charge',
        'managing_agent',
        'asking_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'asking_price' => 'decimal:2',
            'ground_rent' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the owner key for this model.
     */
    protected function getOwnerKey(): string
    {
        return 'seller_id';
    }

    /**
     * Get the seller that owns this property.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get offers for this property.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get viewings for this property.
     */
    public function viewings(): HasMany
    {
        return $this->hasMany(Viewing::class);
    }

    /**
     * Get material information for this property.
     */
    public function materialInformation()
    {
        return $this->hasOne(PropertyMaterialInformation::class);
    }

    /**
     * Get documents for this property.
     */
    public function documents()
    {
        return $this->hasMany(PropertyDocument::class);
    }

    /**
     * Get homecheck data for this property.
     */
    public function homecheckData()
    {
        return $this->hasMany(HomecheckData::class);
    }

    /**
     * Get homecheck reports for this property.
     */
    public function homecheckReports()
    {
        return $this->hasMany(HomecheckReport::class);
    }

    /**
     * Get sales progression records for this property.
     */
    public function salesProgression()
    {
        return $this->hasMany(SalesProgression::class);
    }

    /**
     * Get instruction for this property.
     */
    public function instruction()
    {
        return $this->hasOne(PropertyInstruction::class);
    }
}
