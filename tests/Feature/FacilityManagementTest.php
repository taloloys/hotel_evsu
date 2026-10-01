<?php

use App\Models\Facility;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->landingPerm = Permission::firstOrCreate([
        'permission_key' => 'manage-landing-page',
        'description' => 'Manage landing page and facilities',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->role = Role::firstOrCreate(
        ['role_name' => 'ADMIN'],
        ['description' => 'Admin Role', 'is_active' => true]
    );

    $this->role->permissions()->syncWithoutDetaching([$this->landingPerm->permission_id]);

    $this->admin = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);
});

test('admin can create facility with compressed webp images', function (): void {
    Storage::fake('public');

    $makeFakeImage = function (string $name, int $width = 1600, int $height = 1200) {
        return function_exists('imagecreatetruecolor')
            ? UploadedFile::fake()->image($name, $width, $height)
            : UploadedFile::fake()->create($name, 100, 'image/jpeg');
    };

    $file1 = $makeFakeImage('facility1.jpg', 1600, 1200);
    $file2 = $makeFakeImage('facility2.png', 800, 600);

    $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), [
        'facility_type' => 'single',
        'name' => 'Auditorium Hall',
        'description' => 'Large auditorium for symposiums and conferences.',
        'capacity' => 500,
        'daily_rate' => 25000,
        'rate_type' => 'daily',
        'is_active' => 1,
        'images' => [$file1, $file2],
    ]);

    $response->assertRedirect(route('admin.facilities.index'));

    $facility = Facility::where('name', 'Auditorium Hall')->first();
    expect($facility)->not->toBeNull();
    expect($facility->images)->toHaveCount(2);

    // Verify compressed into WebP
    foreach ($facility->images as $imagePath) {
        expect($imagePath)->toEndWith('.webp');
        expect(Storage::disk('public')->exists($imagePath))->toBeTrue();
        expect(Facility::imageUrl($imagePath))->toContain($imagePath);
    }
});

test('admin can update facility and removes old compressed images', function (): void {
    Storage::fake('public');

    $makeFakeImage = function (string $name) {
        return function_exists('imagecreatetruecolor')
            ? UploadedFile::fake()->image($name)
            : UploadedFile::fake()->create($name, 100, 'image/jpeg');
    };

    $fileOld = $makeFakeImage('old_photo.jpg');
    $oldStoredPath = app(ImageService::class)->compressAndStore($fileOld, 'images/showcase/facilities');
    expect(Storage::disk('public')->exists($oldStoredPath))->toBeTrue();

    $facility = Facility::create([
        'name' => 'Conference Room B',
        'rate' => 1500,
        'rate_type' => 'hourly',
        'capacity' => 25,
        'images' => [$oldStoredPath],
    ]);

    $fileNew = $makeFakeImage('new_photo.jpg');

    $response = $this->actingAs($this->admin)->put(route('admin.facilities.update', $facility), [
        'facility_type' => 'single',
        'name' => 'Conference Room B (Updated)',
        'hourly_rate' => 1800,
        'rate_type' => 'hourly',
        'capacity' => 30,
        'image_paths' => '', // remove old image
        'images' => [$fileNew],
    ]);

    $response->assertRedirect(route('admin.facilities.index'));

    $facility->refresh();
    expect($facility->images)->toHaveCount(1);
    expect($facility->images[0])->toEndWith('.webp');
    expect($facility->images[0])->not->toBe($oldStoredPath);

    // Verify old image was removed from disk
    expect(Storage::disk('public')->exists($oldStoredPath))->toBeFalse();
    expect(Storage::disk('public')->exists($facility->images[0]))->toBeTrue();
});

test('facility images render with accessible URL on facilities list and public landing page', function (): void {
    Storage::fake('public');

    $makeFakeImage = function (string $name) {
        return function_exists('imagecreatetruecolor')
            ? UploadedFile::fake()->image($name)
            : UploadedFile::fake()->create($name, 100, 'image/jpeg');
    };

    $file = $makeFakeImage('pool.jpg');
    $storedPath = app(ImageService::class)->compressAndStore($file, 'images/showcase/facilities');

    $facility = Facility::create([
        'name' => 'Swimming Pool Lounge',
        'rate' => 500,
        'rate_type' => 'hourly',
        'capacity' => 50,
        'images' => [$storedPath],
        'is_active' => true,
    ]);

    // Admin Facilities index
    $response = $this->actingAs($this->admin)->get(route('admin.facilities.index'));
    $response->assertStatus(200);
    $response->assertSee(Facility::imageUrl($storedPath));

    // Public Landing Page
    $responseLanding = $this->get(route('home'));
    $responseLanding->assertStatus(200);
    $responseLanding->assertSee(Facility::imageUrl($storedPath));
});
