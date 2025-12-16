<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HomecheckData extends Model
{
    protected $table = 'homecheck_data';

    public $timestamps = false; // Table only has created_at, not updated_at

    protected $fillable = [
        'property_id',
        'homecheck_report_id',
        'room_name',
        'image_path',
        'is_360',
        'moisture_reading',
        'ai_rating',
        'ai_comments',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_360' => 'boolean',
            'moisture_reading' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the property this homecheck data belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the homecheck report this data belongs to.
     */
    public function homecheckReport(): BelongsTo
    {
        return $this->belongsTo(HomecheckReport::class);
    }

    /**
     * Get the image URL for this homecheck data.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // First, check if S3 is configured and file exists there
        $s3Configured = !empty(config('filesystems.disks.s3.key')) && 
                       !empty(config('filesystems.disks.s3.secret')) && 
                       !empty(config('filesystems.disks.s3.bucket'));
        
        if ($s3Configured) {
            try {
                // Check if file exists in S3
                if (Storage::disk('s3')->exists($this->image_path)) {
                    // For S3, use temporary signed URL (valid for 1 hour) to avoid permission issues
                    // This works even if bucket is private
                    try {
                        $signedUrl = Storage::disk('s3')->temporaryUrl(
                            $this->image_path,
                            now()->addHour()
                        );
                        return $signedUrl;
                    } catch (\Exception $e) {
                        // Fallback to regular URL if signed URL generation fails
                        \Illuminate\Support\Facades\Log::warning('Failed to generate S3 signed URL for homecheck image', [
                            'image_path' => $this->image_path,
                            'error' => $e->getMessage()
                        ]);
                        // Try to get public URL from S3
                        try {
                            return Storage::disk('s3')->url($this->image_path);
                        } catch (\Exception $e2) {
                            \Illuminate\Support\Facades\Log::warning('Failed to get S3 URL for homecheck image', [
                                'image_path' => $this->image_path,
                                'error' => $e2->getMessage()
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Error checking S3 for homecheck image', [
                    'image_path' => $this->image_path,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to local storage if S3 not configured or file not found in S3
        try {
            if (Storage::disk('public')->exists($this->image_path)) {
                return asset('storage/' . $this->image_path);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Error checking local storage for homecheck image', [
                'image_path' => $this->image_path,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }
}
