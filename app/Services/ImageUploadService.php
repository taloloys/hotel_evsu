<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    /**
     * Handle the image upload process.
     *
     * @param  string  $inputName  The name of the file input
     * @param  string  $directory  The directory to store the image in
     * @param  string|null  $existingPath  The existing image path to preserve if no new image is uploaded
     * @return string|null The path to the stored image, or the existing path if no new image is uploaded
     *
     * @throws ValidationException If the upload fails or the file is invalid
     */
    public function handleUpload(Request $request, string $inputName = 'image', string $directory = 'pos/products', ?string $existingPath = null): ?string
    {
        if (! $request->hasFile($inputName)) {
            return $existingPath;
        }

        $file = $request->file($inputName);

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                $inputName => 'The uploaded file is invalid or corrupted.',
            ]);
        }

        // Validate the file using Laravel's validator to enforce constraints (allow up to 10MB to enable compression of larger files)
        $request->validate([
            $inputName => ['image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        return $this->storeFile($file, $directory, $existingPath);
    }

    /**
     * Store the file, compress it, and manage the directory.
     */
    protected function storeFile(UploadedFile $file, string $directory, ?string $existingPath = null): string
    {
        $disk = config('filesystems.uploads_disk', 'public');

        try {
            // Initialize the ImageManager with the GD driver
            $manager = new ImageManager(new Driver);

            // Read the image from the uploaded file's temporary path
            $image = $manager->read($file->getRealPath());

            // Scale down the image if dimensions exceed maximum constraints (800x800), preserving aspect ratio
            $image->scaleDown(width: 800, height: 800);

            // Convert the image to WebP format with 80% quality
            $encodedImage = $image->toWebp(80);

            // Generate unique filename
            $filename = Str::uuid()->toString().'_'.time().'.webp';
            $path = trim($directory, '/').'/'.$filename;

            // Store the optimized image in the configured storage disk
            $stored = Storage::disk($disk)->put($path, (string) $encodedImage);
            if (! $stored) {
                throw new \RuntimeException("Failed to store image to disk [{$disk}].");
            }

            // Delete old file if a new one was successfully uploaded
            if ($existingPath && Storage::disk($disk)->exists($existingPath)) {
                Storage::disk($disk)->delete($existingPath);
            }

            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed: '.$e->getMessage(), [
                'directory' => $directory,
                'disk' => $disk,
                'exception' => $e,
            ]);

            throw ValidationException::withMessages([
                'image' => 'An error occurred while compressing and saving the image. Please try again later.',
            ]);
        }
    }

    /**
     * Delete an image from storage.
     */
    public function deleteImage(?string $path): bool
    {
        $disk = config('filesystems.uploads_disk', 'public');

        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
