<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyMaterialInformation extends Model
{
    protected $table = 'property_material_information';

    protected $fillable = [
        'property_id',
        'heating_type',
        'boiler_age_years',
        'boiler_last_serviced',
        'epc_rating',
        'gas_supply',
        'electricity_supply',
        'mains_water',
        'drainage',
        'known_issues',
        'planning_alterations',
        'documents_uploaded',
    ];

    protected function casts(): array
    {
        return [
            'boiler_last_serviced' => 'date',
            'gas_supply' => 'boolean',
            'electricity_supply' => 'boolean',
            'mains_water' => 'boolean',
            'documents_uploaded' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the property this material information belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
