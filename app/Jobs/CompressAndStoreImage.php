<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\ImageOptimizationService;
use App\Models\HomecheckData;

class CompressAndStoreImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $targetPath;
    public $disk;
    public $homecheckDataId;
    public $maxWidth;
    public $quality;

    /**
     * Create a new job instance.
     *
     * @param string $targetPath Target path in storage (image should already exist here)
     * @param string $disk Storage disk (s3 or public)
     * @param int $homecheckDataId HomecheckData ID to update
     * @param int $maxWidth Maximum width for compression (default: 1920)
     * @param int $quality JPEG quality 0-100 (default: 85)
     */
    public function __construct($targetPath, $disk, $homecheckDataId, $maxWidth = 1920, $quality = 85)
    {
        $this->targetPath = $targetPath;
        $this->disk = $disk;
        $this->homecheckDataId = $homecheckDataId;
        $this->maxWidth = $maxWidth;
        $this->quality = $quality;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tempFile = null;
        try {
            Log::info('Starting image compression job', [
                'target_path' => $this->targetPath,
                'disk' => $this->disk,
                'homecheck_data_id' => $this->homecheckDataId,
            ]);

            // Determine target storage disk
            $targetDisk = Storage::disk($this->disk);
            
            // Check if the image already exists at target path
            if (!$targetDisk->exists($this->targetPath)) {
                throw new \Exception('Image file not found at target path: ' . $this->targetPath);
            }

            // Read the existing image from target location
            $imageContent = $targetDisk->get($this->targetPath);
            if (empty($imageContent)) {
                throw new \Exception('Image file is empty at: ' . $this->targetPath);
            }
            
            $extension = strtolower(pathinfo($this->targetPath, PATHINFO_EXTENSION));
            $tempFile = tempnam(sys_get_temp_dir(), 'img_') . '.' . $extension;
            
            if (file_put_contents($tempFile, $imageContent) === false) {
                throw new \Exception('Failed to write temporary file: ' . $tempFile);
            }
            
            if (!file_exists($tempFile) || filesize($tempFile) == 0) {
                throw new \Exception('Temporary file was not created or is empty: ' . $tempFile);
            }
            
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                throw new \Exception('GD extension is not loaded. Please install php-gd extension.');
            }
            
            // Use Intervention Image to compress
            try {
                // Try Intervention Image v3 API first
                if (class_exists('\Intervention\Image\ImageManager') && class_exists('\Intervention\Image\Drivers\Gd\Driver')) {
                    // Intervention Image v3
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($tempFile);
                    
                    // Get original dimensions
                    $width = $image->width();
                    
                    // Resize if larger than max width (maintain aspect ratio)
                    if ($width > $this->maxWidth) {
                        $image->scale(width: $this->maxWidth);
                    }
                    
                    // Get file extension to determine format
                    $targetExtension = strtolower(pathinfo($this->targetPath, PATHINFO_EXTENSION));
                    
                    // Convert based on format
                    // In Intervention Image v3, toJpeg() and toPng() return EncodedImage which can be cast to string
                    if (in_array($targetExtension, ['jpg', 'jpeg'])) {
                        $optimizedContent = (string) $image->toJpeg($this->quality);
                    } elseif ($targetExtension === 'png') {
                        $optimizedContent = (string) $image->toPng();
                    } else {
                        $optimizedContent = file_get_contents($tempFile);
                    }
                } elseif (class_exists('\Intervention\Image\ImageManagerStatic')) {
                    // Intervention Image v2 (legacy)
                    $image = \Intervention\Image\ImageManagerStatic::make($tempFile);
                    
                    // Get original dimensions
                    $width = $image->width();
                    
                    // Resize if larger than max width (maintain aspect ratio)
                    if ($width > $this->maxWidth) {
                        $image->resize($this->maxWidth, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    
                    // Get file extension to determine format
                    $targetExtension = strtolower(pathinfo($this->targetPath, PATHINFO_EXTENSION));
                    
                    // Convert based on format
                    if (in_array($targetExtension, ['jpg', 'jpeg'])) {
                        $optimizedContent = (string) $image->encode('jpg', $this->quality);
                    } elseif ($targetExtension === 'png') {
                        $optimizedContent = (string) $image->encode('png');
                    } else {
                        $optimizedContent = file_get_contents($tempFile);
                    }
                } else {
                    throw new \Exception('Intervention Image library not found or incompatible version');
                }
            } catch (\Exception $e) {
                Log::warning('Intervention Image processing failed, using original file', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'temp_file' => $tempFile,
                    'trace' => $e->getTraceAsString(),
                ]);
                // Fallback: use original file if image processing fails
                $optimizedContent = file_get_contents($tempFile);
            }
            
            if (empty($optimizedContent)) {
                throw new \Exception('Optimized content is empty');
            }
            
            // Replace the existing image with compressed version (in-place optimization)
            $uploaded = $targetDisk->put($this->targetPath, $optimizedContent);
            
            if (!$uploaded) {
                throw new \Exception('Failed to update compressed image in storage disk: ' . $this->disk);
            }
            
            Log::info('Image compressed and updated in storage', [
                'disk' => $this->disk,
                'path' => $this->targetPath,
                'original_size' => strlen($imageContent),
                'compressed_size' => strlen($optimizedContent),
                'savings' => round((1 - strlen($optimizedContent) / strlen($imageContent)) * 100, 2) . '%',
            ]);
            
            // Clean up temporary file
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            // Image path remains the same, no need to update HomecheckData
            Log::info('Image compression completed successfully', [
                'homecheck_data_id' => $this->homecheckDataId,
                'path' => $this->targetPath,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image compression job failed', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'target_path' => $this->targetPath,
                'disk' => $this->disk ?? 'unknown',
                'homecheck_data_id' => $this->homecheckDataId ?? 'unknown',
                'temp_file_exists' => $tempFile ? file_exists($tempFile) : false,
                'gd_loaded' => extension_loaded('gd'),
            ]);
            
            // Clean up on error
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            throw $e;
        }
    }
}
