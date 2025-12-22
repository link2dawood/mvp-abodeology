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

    protected $tempFilePath;
    protected $targetPath;
    protected $disk;
    protected $homecheckDataId;
    protected $maxWidth;
    protected $quality;

    /**
     * Create a new job instance.
     *
     * @param string $tempFilePath Temporary file path
     * @param string $targetPath Target path in storage
     * @param string $disk Storage disk (s3 or public)
     * @param int $homecheckDataId HomecheckData ID to update
     * @param int $maxWidth Maximum width for compression (default: 1920)
     * @param int $quality JPEG quality 0-100 (default: 85)
     */
    public function __construct($tempFilePath, $targetPath, $disk, $homecheckDataId, $maxWidth = 1920, $quality = 85)
    {
        $this->tempFilePath = $tempFilePath;
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
                'temp_path' => $this->tempFilePath,
                'target_path' => $this->targetPath,
                'disk' => $this->disk,
                'homecheck_data_id' => $this->homecheckDataId,
            ]);

            // Get the temporary file
            $storage = Storage::disk('local');
            
            if (!$storage->exists($this->tempFilePath)) {
                throw new \Exception('Temporary file not found: ' . $this->tempFilePath);
            }

            // Read the temporary file
            $imageContent = $storage->get($this->tempFilePath);
            if (empty($imageContent)) {
                throw new \Exception('Temporary file is empty: ' . $this->tempFilePath);
            }
            
            $extension = strtolower(pathinfo($this->tempFilePath, PATHINFO_EXTENSION));
            $tempFile = tempnam(sys_get_temp_dir(), 'img_') . '.' . $extension;
            
            if (file_put_contents($tempFile, $imageContent) === false) {
                throw new \Exception('Failed to write temporary file: ' . $tempFile);
            }
            
            if (!file_exists($tempFile) || filesize($tempFile) == 0) {
                throw new \Exception('Temporary file was not created or is empty: ' . $tempFile);
            }

            // Determine target storage disk
            $targetDisk = Storage::disk($this->disk);
            
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
                    if (in_array($targetExtension, ['jpg', 'jpeg'])) {
                        $optimizedContent = (string) $image->toJpeg($this->quality)->encode();
                    } elseif ($targetExtension === 'png') {
                        $optimizedContent = (string) $image->toPng()->encode();
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
            
            // Upload compressed image to target storage
            $uploaded = $targetDisk->put($this->targetPath, $optimizedContent);
            
            if (!$uploaded) {
                throw new \Exception('Failed to upload image to storage disk: ' . $this->disk);
            }
            
            Log::info('Image uploaded to storage', [
                'disk' => $this->disk,
                'path' => $this->targetPath,
                'size' => strlen($optimizedContent),
            ]);
            
            // Clean up temporary file
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            // Delete temporary storage file
            try {
                $storage->delete($this->tempFilePath);
            } catch (\Exception $e) {
                Log::warning('Failed to delete temporary storage file', [
                    'path' => $this->tempFilePath,
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Update HomecheckData with the compressed path
            $homecheckData = HomecheckData::find($this->homecheckDataId);
            if ($homecheckData) {
                $homecheckData->update([
                    'image_path' => $this->targetPath,
                ]);
                Log::info('Image compression completed and HomecheckData updated', [
                    'homecheck_data_id' => $this->homecheckDataId,
                    'final_path' => $this->targetPath,
                ]);
            } else {
                Log::warning('HomecheckData not found after compression', [
                    'homecheck_data_id' => $this->homecheckDataId,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Image compression job failed', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'temp_path' => $this->tempFilePath,
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
