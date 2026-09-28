<?php

use App\Models\LandingPageShowcase;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->landingPerm = Permission::firstOrCreate([
        'permission_key' => 'manage-landing-page',
        'description' => 'Manage landing page',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->userPerm = Permission::firstOrCreate([
        'permission_key' => 'manage-users',
        'description' => 'Manage users',
        'module' => 'System',
        'is_active' => true,
    ]);
});

function createAdminWithPermissions(): User
{
    $role = Role::create(['role_name' => 'ADMIN', 'description' => 'Admin']);
    $role->permissions()->sync(Permission::whereIn('permission_key', ['manage-landing-page', 'manage-users'])->pluck('permission_id'));

    return User::factory()->create(['role_id' => $role->role_id, 'is_active' => true]);
}

it('allows admin users to access the landing page control panel', function (): void {
    $admin = createAdminWithPermissions();

    $response = $this->actingAs($admin)->get(route('admin.landing-page'));

    $response->assertStatus(200);
    $response->assertSee('Public Showcase Landing Page Control');
});

it('renders the landing page link in the admin sidebar navigation', function (): void {
    $admin = createAdminWithPermissions();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('admin.landing-page'));
    $response->assertSee('Landing Page');
});

it('allows admin to update showcase room configuration with multiple images', function (): void {
    Storage::fake('public');
    $admin = createAdminWithPermissions();

    $showcase = LandingPageShowcase::create([
        'type' => 'ROOM',
        'title' => 'Deluxe Room',
        'category' => 'Deluxe',
        'price_rate' => '',
        'capacity' => '2 Guests',
        'images' => ['images/showcase/rooms/deluxe.jpg'],
    ]);

    $file1 = UploadedFile::fake()->image('room1.jpg');
    $file2 = UploadedFile::fake()->image('room2.jpg');

    $response = $this->actingAs($admin)->patch(route('admin.landing-page.room.update', $showcase->id), [
        'category' => 'Premium Deluxe',
        'capacity' => '3 Guests',
        'badge' => 'Exclusive',
        'images' => [$file1, $file2],
    ]);

    $response->assertRedirect(route('admin.landing-page'));
    $this->assertDatabaseHas('landing_page_showcases', [
        'id' => $showcase->id,
        'category' => 'Premium Deluxe',
        'capacity' => '3 Guests',
        'badge' => 'Exclusive',
    ]);
});

it('invalidates showcase cache when landing page showcase items are updated', function (): void {
    Cache::put('public_showcase_data', ['test' => 'cached_data'], 3600);

    $admin = createAdminWithPermissions();

    $showcase = LandingPageShowcase::create([
        'type' => 'ROOM',
        'title' => 'Standard Room',
        'category' => 'Standard',
        'price_rate' => '₱2,000',
        'capacity' => '2 Guests',
        'images' => ['images/showcase/rooms/standard.jpg'],
    ]);

    $this->actingAs($admin)->patch(route('admin.landing-page.room.update', $showcase->id), [
        'category' => 'Standard Updated',
        'capacity' => '2 Guests',
    ]);

    expect(Cache::has('public_showcase_data'))->toBeFalse();
});
