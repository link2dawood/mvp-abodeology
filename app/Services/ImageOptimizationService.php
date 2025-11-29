<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageOptimizationService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize and save an uploaded image.
     * 
     * @param UploadedFile $file
     * @param string $path
     * @param string $disk
     * @param int $maxWidth Maximum width in pixels (default: 1920)
     * @param int $quality JPEG quality 0-100 (default: 85)
     * @return string The path to the optimized image
     */
    public function optimizeAndSave(UploadedFile $file, string $path, string $disk = 'public', int $maxWidth = 1920, int $quality = 85): string
    {
        // Read the image
        $image = $this->manager->read($file->getRealPath());
        
        // Get original dimensions
        $width = $image->width();
        $height = $image->height();
        
        // Resize if larger than max width (maintain aspect ratio)
        if ($width > $maxWidth) {
            $image->scale(width: $maxWidth);
        }
        
        // Convert to RGB if needed (for JPEG)
        if ($file->getMimeType() === 'image/jpeg' || $file->getMimeType() === 'image/jpg') {
            $image->toJpeg($quality);
        } elseif ($file->getMimeType() === 'image/png') {
            // PNG optimization - convert to JPEG for smaller file size (optional)
            // For now, keep PNG but optimize
            $image->toPng();
        }
        
        // Save optimized image
        $optimizedContent = (string) $image->encode();
        $fullPath = Storage::disk($disk)->path($path);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save the optimized image
        file_put_contents($fullPath, $optimizedContent);
        
        return $path;
    }

    /**
     * Optimize image from existing file path.
     * 
     * @param string $filePath
     * @param string $disk
     * @param int $maxWidth
     * @param int $quality
     * @return bool Success status
     */
    public function optimizeExisting(string $filePath, string $disk = 'public', int $maxWidth = 1920, int $quality = 85): bool
    {
        try {
            if (!Storage::disk($disk)->exists($filePath)) {
                return false;
            }
            
            $fullPath = Storage::disk($disk)->path($filePath);
            $image = $this->manager->read($fullPath);
            
            $width = $image->width();
            
            if ($width > $maxWidth) {
                $image->scale(width: $maxWidth);
            }
            
            // Determine format from extension
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image->toJpeg($quality);
            } elseif ($extension === 'png') {
                $image->toPng();
            }
            
            $optimizedContent = (string) $image->encode();
            file_put_contents($fullPath, $optimizedContent);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create thumbnail version of image.
     * 
     * @param string $filePath
     * @param string $disk
     * @param int $width
     * @param int $height
     * @return string|null Thumbnail path or null on failure
     */
    public function createThumbnail(string $filePath, string $disk = 'public', int $width = 300, int $height = 300): ?string
    {
        try {
            if (!Storage::disk($disk)->exists($filePath)) {
                return null;
            }
            
            $fullPath = Storage::disk($disk)->path($filePath);
            $image = $this->manager->read($fullPath);
            
            // Create thumbnail
            $image->cover($width, $height);
            
            // Generate thumbnail path
            $pathInfo = pathinfo($filePath);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            
            $thumbnailFullPath = Storage::disk($disk)->path($thumbnailPath);
            
            // Ensure directory exists
            $directory = dirname($thumbnailFullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Save thumbnail
            $extension = strtolower($pathInfo['extension']);
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image->toJpeg(85);
            } elseif ($extension === 'png') {
                $image->toPng();
            }
            
            $thumbnailContent = (string) $image->encode();
            file_put_contents($thumbnailFullPath, $thumbnailContent);
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return null;
        }
    }
}

