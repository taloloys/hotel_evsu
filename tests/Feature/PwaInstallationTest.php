<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders PWA manifest and service worker on the public showcase page', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('site.webmanifest');
    $response->assertSee('sw.js');
    $response->assertSee('serviceWorker');
});

it('renders PWA manifest and service worker on the login page', function (): void {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('site.webmanifest');
    $response->assertSee('sw.js');
    $response->assertSee('serviceWorker');
});

it('renders PWA manifest and service worker on authenticated dashboard layout', function (): void {
    $role = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Administrator',
        'is_active' => true,
    ]);

    $perm = Permission::create([
        'permission_key' => 'manage-users',
        'description' => 'Manage Users',
        'module' => 'System',
        'is_active' => true,
    ]);
    $role->permissions()->sync([$perm->permission_id]);

    $admin = User::factory()->create([
        'role_id' => $role->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertSee('site.webmanifest');
    $response->assertSee('sw.js');
    $response->assertSee('serviceWorker');
});

it('has a valid web manifest with required PWA standalone parameters and icons', function (): void {
    $manifestPath = public_path('site.webmanifest');
    expect(file_exists($manifestPath))->toBeTrue();

    $content = json_decode(file_get_contents($manifestPath), true);
    expect($content)->toBeArray();
    expect($content['display'])->toBe('standalone');
    expect($content['start_url'])->toBe('./');
    expect($content['icons'])->toBeArray();

    $sizes = array_column($content['icons'], 'sizes');
    expect($sizes)->toContain('192x192');
    expect($sizes)->toContain('512x512');
});

it('has the service worker and offline fallback files in public directory', function (): void {
    expect(file_exists(public_path('sw.js')))->toBeTrue();
    expect(file_exists(public_path('offline.html')))->toBeTrue();
});
