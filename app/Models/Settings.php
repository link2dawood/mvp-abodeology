<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', string $description = null): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number', 'integer' => is_numeric($value) ? (int) $value : $value,
            'float', 'decimal' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
            'array' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Get all public settings
     */
    public static function getPublic(): array
    {
        return self::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }
}


