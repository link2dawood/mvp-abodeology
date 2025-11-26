<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmlDocument extends Model
{
    protected $fillable = [
        'aml_check_id',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the AML check this document belongs to.
     */
    public function amlCheck(): BelongsTo
    {
        return $this->belongsTo(AmlCheck::class);
    }

    /**
     * Get the full URL for the document.
     */
    public function getUrlAttribute(): string
    {
        $disk = config('filesystems.default');
        
        if ($disk === 's3') {
            return \Storage::disk('s3')->url($this->file_path);
        }
        
        return \Storage::disk('public')->url($this->file_path);
    }
}
