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
        // Ensure directory exists
        $storagePath = storage_path('app/public/'.trim($directory, '/'));

        if (! is_dir($storagePath)) {
            // Create directory with appropriate permissions
            mkdir($storagePath, 0775, true);
        }

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

            // Store the optimized image in the storage system (e.g., 'public' disk)
            Storage::disk('public')->put($path, (string) $encodedImage);

            // Delete old file if a new one was successfully uploaded
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed: '.$e->getMessage(), [
                'directory' => $directory,
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
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
