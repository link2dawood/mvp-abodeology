<?php

namespace App\Models;

use App\Traits\Ownable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use Ownable;

    /**
     * Property status constants.
     * These define all valid property statuses in the system.
     * 
     * Status Workflow:
     * draft → property_details_captured → pre_marketing → signed → awaiting_aml → live → sstc → sold
     *                                                                              ↓
     *                                                                         withdrawn
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROPERTY_DETAILS_CAPTURED = 'property_details_captured';
    public const STATUS_PRE_MARKETING = 'pre_marketing';
    public const STATUS_SIGNED = 'signed';
    public const STATUS_AWAITING_AML = 'awaiting_aml';
    public const STATUS_LIVE = 'live';
    public const STATUS_SSTC = 'sstc';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_SOLD = 'sold';

    /**
     * Get all valid property statuses.
     * 
     * @return array
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PROPERTY_DETAILS_CAPTURED,
            self::STATUS_PRE_MARKETING,
            self::STATUS_SIGNED,
            self::STATUS_AWAITING_AML,
            self::STATUS_LIVE,
            self::STATUS_SSTC,
            self::STATUS_WITHDRAWN,
            self::STATUS_SOLD,
        ];
    }

    /**
     * Check if a status value is valid.
     * 
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::getValidStatuses());
    }

    protected $fillable = [
        'seller_id',
        'assigned_agent_id',
        'seller2_name',
        'seller2_email',
        'seller2_phone',
        'address',
        'postcode',
        'property_type',
        'bedrooms',
        'bathrooms',
        'reception_rooms',
        'outbuildings',
        'garden_details',
        'parking',
        'parking_options',
        'tenure',
        'lease_years_remaining',
        'ground_rent',
        'service_charge',
        'managing_agent',
        'solicitor_name',
        'solicitor_firm',
        'solicitor_email',
        'solicitor_phone',
        'solicitor_details_completed',
        'asking_price',
        'pricing_notes',
        'status',
        'with_keys',
    ];

    protected function casts(): array
    {
        return [
            'asking_price' => 'decimal:2',
            'ground_rent' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'parking_options' => 'array',
            'with_keys' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     * Add validation for status field.
     */
    protected static function boot()
    {
        parent::boot();

        // Validate status when saving
        static::saving(function ($property) {
            if ($property->isDirty('status')) {
                if (!self::isValidStatus($property->status)) {
                    throw new \InvalidArgumentException(
                        "Invalid property status: '{$property->status}'. Valid statuses: " . 
                        implode(', ', self::getValidStatuses())
                    );
                }
            }
        });
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
     * Get photos for this property.
     */
    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    /**
     * Get primary photo for this property.
     */
    public function primaryPhoto()
    {
        return $this->hasOne(PropertyPhoto::class)->where('is_primary', true);
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

    /**
     * Get the assigned agent (primary agent) for this property.
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Get all agents assigned to this property (via pivot table).
     */
    public function agents()
    {
        return $this->belongsToMany(User::class, 'property_agents', 'property_id', 'agent_id')
            ->withPivot(['assigned_by', 'assigned_at', 'is_primary', 'notes'])
            ->withTimestamps()
            ->whereIn('role', ['admin', 'agent']); // Only agents and admins
    }

    /**
     * Get primary agent for this property (via pivot table).
     */
    public function primaryAgent()
    {
        return $this->belongsToMany(User::class, 'property_agents', 'property_id', 'agent_id')
            ->withPivot(['assigned_by', 'assigned_at', 'is_primary', 'notes'])
            ->wherePivot('is_primary', true)
            ->whereIn('role', ['admin', 'agent'])
            ->first();
    }
}
