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
        UploadedFile $file,
        string $path,
        ?int $maxWidth = 800,
        ?int $maxHeight = 800,
        int $quality = 80
    ): string {
        // Initialize the ImageManager with the GD driver
        $manager = new ImageManager(new Driver);

        // Read the image from the uploaded file's temporary path
        $image = $manager->read($file->getRealPath());

        // Scale down the image if dimensions exceed maximum constraints, preserving aspect ratio
        if ($maxWidth !== null && $maxHeight !== null) {
            $image->scaleDown(width: $maxWidth, height: $maxHeight);
        }

        // Convert the image to WebP format with the specified quality
        $encodedImage = $image->toWebp($quality);

        // Generate a unique, collision-proof filename
        $filename = trim($path, '/').'/'.Str::uuid()->toString().'_'.time().'.webp';

        // Store the optimized image in the storage system (e.g., 'public' disk)
        Storage::disk('public')->put($filename, (string) $encodedImage);

        return $filename;
    }

    /**
     * Delete an image from storage.
     *
     * @param  string|null  $path  The path of the image to delete
     */
    public function deleteImage(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
