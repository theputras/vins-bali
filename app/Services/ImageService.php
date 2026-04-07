<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    private ImageManager $manager;

    /**
     * Max width for resized images.
     */
    private int $maxWidth = 1920;

    /**
     * Max height for resized images.
     */
    private int $maxHeight = 1080;

    /**
     * JPEG/WebP quality (1-100).
     */
    private int $quality = 80;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Process and store an uploaded image.
     * Automatically resizes and compresses the image.
     *
     * @param  UploadedFile  $file  The uploaded image file
     * @param  string  $directory  Storage path (relative to public disk)
     * @return string The stored file path (relative to public disk)
     */
    public function processAndStore(UploadedFile $file, string $directory): string
    {
        $image = $this->manager->decode($file->getPathname());

        // Resize if larger than max dimensions (maintains aspect ratio)
        $image->scaleDown(width: $this->maxWidth, height: $this->maxHeight);

        // Encode to WebP for optimal compression
        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder($this->quality));

        // Generate unique filename
        $filename = uniqid().'_'.time().'.webp';
        $path = rtrim($directory, '/').'/'.$filename;

        // Store to public disk
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Process and store an uploaded image as a thumbnail.
     *
     * @param  UploadedFile  $file  The uploaded image file
     * @param  string  $directory  Storage path (relative to public disk)
     * @return string The stored file path (relative to public disk)
     */
    public function processAndStoreThumbnail(UploadedFile $file, string $directory, int $width = 400, int $height = 300): string
    {
        $image = $this->manager->decode($file->getPathname());

        // Cover crop for consistent thumbnail sizes
        $image->cover($width, $height);

        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder($this->quality));

        $filename = 'thumb_'.uniqid().'_'.time().'.webp';
        $path = rtrim($directory, '/').'/'.$filename;

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
