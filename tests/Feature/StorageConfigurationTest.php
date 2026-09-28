<?php

use App\Models\LandingPageShowcase;
use App\Models\PosProduct;
use App\Services\BackupStorageService;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('PosProduct image_url attribute behaves correctly across disks and url types', function (): void {
    // 1. Null image_path
    $product = new PosProduct(['image_path' => null]);
    expect($product->image_url)->toBeNull();

    // 2. Direct external URL
    $product->image_path = 'https://images.unsplash.com/photo-hotel.jpg';
    expect($product->image_url)->toBe('https://images.unsplash.com/photo-hotel.jpg');

    // 3. Local public disk
    Storage::fake('public');
    Config::set('filesystems.uploads_disk', 'public');
    $product->image_path = 'pos/products/coffee.webp';
    expect($product->image_url)->toContain('pos/products/coffee.webp');

    // 4. S3 disk configuration
    Storage::fake('s3');
    Config::set('filesystems.uploads_disk', 's3');
    $product->image_path = 'pos/products/coffee.webp';
    expect($product->image_url)->toContain('pos/products/coffee.webp');
});

test('LandingPageShowcase url helper resolves static vs uploaded images correctly', function (): void {
    // Null or empty
    expect(LandingPageShowcase::url(null))->toBeNull();
    expect(LandingPageShowcase::url(''))->toBeNull();

    // Direct HTTP(S) URL
    expect(LandingPageShowcase::url('https://cdn.example.com/banner.jpg'))
        ->toBe('https://cdn.example.com/banner.jpg');

    // Static application fallback asset (bundled in repository public folder)
    $staticPath = 'images/logo.png';
    expect(LandingPageShowcase::imageExists($staticPath))->toBeTrue();
    expect(LandingPageShowcase::url($staticPath))->toBe(asset($staticPath));

    // Uploaded dynamic showcase image
    $uploadedPath = 'images/showcase/rooms/room_deluxe.webp';

    Storage::fake('public');
    Config::set('filesystems.uploads_disk', 'public');
    expect(LandingPageShowcase::url($uploadedPath))->toContain('images/showcase/rooms/room_deluxe.webp');
});

test('ImageUploadService uploads and deletes images using configured uploads disk', function (): void {
    Storage::fake('public');
    Config::set('filesystems.uploads_disk', 'public');

    $file = UploadedFile::fake()->image('tea.jpg', 600, 600);
    $request = Request::create('/upload', 'POST', [], [], [
        'image' => $file,
    ]);

    $service = new ImageUploadService;
    $path = $service->handleUpload($request, 'image', 'pos/products');

    expect($path)->toBeString();
    expect($path)->toStartWith('pos/products/');
    expect(Storage::disk('public')->exists($path))->toBeTrue();

    // Test deletion
    $deleted = $service->deleteImage($path);
    expect($deleted)->toBeTrue();
    expect(Storage::disk('public')->exists($path))->toBeFalse();
});

test('BackupStorageService operates seamlessly on S3 disk', function (): void {
    Storage::fake('s3');
    Config::set('filesystems.backup_disk', 's3');

    $service = new BackupStorageService;

    // Create a dummy temp file to store
    $tempFile = tempnam(sys_get_temp_dir(), 'backup_test_');
    file_put_contents($tempFile, 'CREATE TABLE test (id INT);');

    $backupFilename = 'backup_2026-09-28_test.sql';
    $stored = $service->store($tempFile, $backupFilename);
    @unlink($tempFile);

    expect($stored)->toBeTrue();
    expect($service->exists($backupFilename))->toBeTrue();

    // Test list
    $list = $service->list();
    expect($list)->toHaveCount(1);
    expect($list[0]['filename'])->toBe($backupFilename);
    expect($list[0]['raw_size'])->toBeGreaterThan(0);

    // Test copy to local path for restore operations
    $localCopy = $service->getLocalCopy($backupFilename);
    expect($localCopy)->not->toBeNull();
    expect(file_exists($localCopy))->toBeTrue();
    expect(file_get_contents($localCopy))->toContain('CREATE TABLE test');
    @unlink($localCopy);

    // Clean with 0 days should delete everything
    $deletedCount = $service->cleanup(0);
    expect($deletedCount)->toBeGreaterThanOrEqual(1);

    // Verify deletion
    expect($service->exists($backupFilename))->toBeFalse();
});
