<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * Compress and upload an image.
     *
     * @param  UploadedFile  $file  The uploaded file from the request
     * @param  string  $path  The base path to store the image
     * @param  int|null  $maxWidth  Maximum width of the image
     * @param  int|null  $maxHeight  Maximum height of the image
     * @param  int  $quality  WebP compression quality (0-100)
     * @return string The stored file path relative to the disk root
     */
    public function compressAndStore(
        UploadedFile|string $file,
        string $path,
        ?int $maxWidth = 1200,
        ?int $maxHeight = 800,
        int $quality = 80
    ): string {
        // Initialize the ImageManager with the GD driver
        $manager = new ImageManager(new Driver);

        // Read the image from the uploaded file's temporary path or file path
        $source = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $image = $manager->read($source);

        // Scale down the image if dimensions exceed maximum constraints, preserving aspect ratio
        if ($maxWidth !== null && $maxHeight !== null) {
            $image->scaleDown(width: $maxWidth, height: $maxHeight);
        }

        // Convert the image to WebP format with the specified quality
        $encodedImage = $image->toWebp($quality);

        // Generate a unique, collision-proof filename
        $filename = trim($path, '/').'/'.Str::uuid()->toString().'_'.time().'.webp';

        // Store the optimized image in the storage system
        $disk = config('filesystems.uploads_disk', 'public');
        Storage::disk($disk)->put($filename, (string) $encodedImage);

        return $filename;
    }

    /**
     * Delete an image from storage.
     *
     * @param  string|null  $path  The path of the image to delete
     */
    public function deleteImage(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        $disk = config('filesystems.uploads_disk', 'public');
        $cleanPath = ltrim(preg_replace('#^storage/#', '', ltrim($path, '/')), '/');

        if (Storage::disk($disk)->exists($cleanPath)) {
            return Storage::disk($disk)->delete($cleanPath);
        }

        return false;
    }
}
