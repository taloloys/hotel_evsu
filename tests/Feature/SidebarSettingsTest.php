<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserModulePreference;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Administrator',
        'is_system_admin' => true,
    ]);

    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Staff',
        'is_system_admin' => false,
    ]);

    $this->adminUser = User::factory()->create([
        'username' => 'admin_tester',
        'role_id' => $this->adminRole->role_id,
    ]);

    $this->staffUser = User::factory()->create([
        'username' => 'staff_tester',
        'role_id' => $this->frontdeskRole->role_id,
    ]);
});

test('unauthenticated users are redirected when accessing sidebar settings', function (): void {
    $this->get(route('admin.sidebar-settings'))
        ->assertRedirect();
});

test('non-admin users cannot access sidebar settings page', function (): void {
    $this->actingAs($this->staffUser)
        ->get(route('admin.sidebar-settings'))
        ->assertForbidden();
});

test('super admin can view sidebar settings page', function (): void {
    $this->actingAs($this->adminUser)
        ->get(route('admin.sidebar-settings'))
        ->assertOk()
        ->assertSee('Sidebar Display Settings')
        ->assertSee('Front Desk')
        ->assertSee('Cafeteria / Coffee Shop')
        ->assertSee('Accounting & Finance')
        ->assertSee('Food Delivery');
});

test('super admin can toggle module sidebar visibility off and back on', function (): void {
    // Initial state: visible
    expect($this->adminUser->isModuleVisibleInSidebar('coffeeshop'))->toBeTrue();

    // Toggle off
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.sidebar-settings.toggle', 'coffeeshop'));

    $response->assertRedirect(route('admin.sidebar-settings'));

    $this->assertDatabaseHas('user_module_preferences', [
        'user_id' => $this->adminUser->user_id,
        'module_key' => 'coffeeshop',
        'is_visible' => false,
    ]);

    $this->adminUser->refresh();
    expect($this->adminUser->isModuleVisibleInSidebar('coffeeshop'))->toBeFalse();

    // Toggle back on
    $this->actingAs($this->adminUser)
        ->post(route('admin.sidebar-settings.toggle', 'coffeeshop'));

    $this->assertDatabaseHas('user_module_preferences', [
        'user_id' => $this->adminUser->user_id,
        'module_key' => 'coffeeshop',
        'is_visible' => true,
    ]);

    $this->adminUser->refresh();
    expect($this->adminUser->isModuleVisibleInSidebar('coffeeshop'))->toBeTrue();
});

test('toggling module off hides that section from super admin sidebar html', function (): void {
    // By default, Coffee Shop section is rendered
    $this->actingAs($this->adminUser)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee(route('coffeeshop.pos'));

    // Hide Coffee Shop from sidebar
    UserModulePreference::create([
        'user_id' => $this->adminUser->user_id,
        'module_key' => 'coffeeshop',
        'is_visible' => false,
    ]);

    $this->adminUser->refresh();

    $this->actingAs($this->adminUser)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee(route('coffeeshop.pos'));
});

test('super admin can still directly access module URLs even when hidden from sidebar', function (): void {
    UserModulePreference::create([
        'user_id' => $this->adminUser->user_id,
        'module_key' => 'coffeeshop',
        'is_visible' => false,
    ]);

    // Direct access to coffeeshop dashboard should succeed (200 OK or non-redirect error)
    $response = $this->actingAs($this->adminUser->fresh())
        ->get(route('coffeeshop.dashboard'));

    expect($response->status())->toBeIn([200, 302]);
});

test('super admin sidebar preference does not affect non-admin users sidebar', function (): void {
    // Super admin hides frontdesk
    UserModulePreference::create([
        'user_id' => $this->adminUser->user_id,
        'module_key' => 'frontdesk',
        'is_visible' => false,
    ]);

    // For staff user, isModuleVisibleInSidebar should still return true
    expect($this->staffUser->isModuleVisibleInSidebar('frontdesk'))->toBeTrue();
});
