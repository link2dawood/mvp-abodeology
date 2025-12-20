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
            $tempFile = tempnam(sys_get_temp_dir(), 'img_') . '.' . pathinfo($this->tempFilePath, PATHINFO_EXTENSION);
            file_put_contents($tempFile, $imageContent);

            // Determine target storage disk
            $targetDisk = Storage::disk($this->disk);
            
            // Use Intervention Image to compress
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($tempFile);
            
            // Get original dimensions
            $width = $image->width();
            
            // Resize if larger than max width (maintain aspect ratio)
            if ($width > $this->maxWidth) {
                $image->scale(width: $this->maxWidth);
            }
            
            // Get file extension to determine format
            $extension = strtolower(pathinfo($this->targetPath, PATHINFO_EXTENSION));
            
            // Convert based on format
            $optimizedContent = null;
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $optimizedContent = (string) $image->toJpeg($this->quality)->encode();
            } elseif ($extension === 'png') {
                $optimizedContent = (string) $image->toPng()->encode();
            } else {
                // For other formats, just use as is
                $optimizedContent = file_get_contents($tempFile);
            }
            
            // Upload compressed image to target storage
            $targetDisk->put($this->targetPath, $optimizedContent);
            
            // Clean up temporary file
            @unlink($tempFile);
            
            // Delete temporary storage file
            $storage->delete($this->tempFilePath);
            
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
                'trace' => $e->getTraceAsString(),
                'temp_path' => $this->tempFilePath,
                'target_path' => $this->targetPath,
            ]);
            
            // Clean up on error
            @unlink($tempFile ?? null);
            
            throw $e;
        }
    }
}
