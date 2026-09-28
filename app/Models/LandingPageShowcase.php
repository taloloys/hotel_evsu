<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingPageShowcase extends Model
{
    protected $fillable = [
        'type',
        'title',
        'category',
        'price_rate',
        'capacity',
        'badge',
        'timing',
        'icon',
        'images',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Resolve public web URL for a showcase image, supporting:
     * 1. Full URLs (http/https)
     * 2. Shipped static repo assets in public/
     * 3. Persistent uploaded assets on the configured uploads disk (local public or cloud S3)
     */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        $diskName = config('filesystems.uploads_disk', 'public');
        $disk = Storage::disk($diskName);

        if (config("filesystems.disks.{$diskName}.driver") === 's3') {
            $days = config('filesystems.url_expiration_days', 7);

            return $disk->temporaryUrl($path, now()->addDays($days));
        }

        return $disk->url($path);
    }

    /**
     * Check if a showcase image exists either as a static repo asset or on the configured uploads disk.
     */
    public static function imageExists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return true;
        }

        if (file_exists(public_path($path))) {
            return true;
        }

        $disk = config('filesystems.uploads_disk', 'public');

        return Storage::disk($disk)->exists($path);
    }
}
