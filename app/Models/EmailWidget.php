<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'category',
        'html',
        'locked',
        'description',
    ];

    protected $casts = [
        'locked' => 'boolean',
    ];

    /**
     * Get widgets by category.
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)->orderBy('name')->get();
    }

    /**
     * Get all categories.
     */
    public static function getCategories(): array
    {
        return static::distinct()->pluck('category')->toArray();
    }
}
