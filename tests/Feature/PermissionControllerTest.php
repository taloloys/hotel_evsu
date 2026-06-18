<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Set up test roles
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
    ]);

    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Staff Role',
    ]);

    // Create system permission
    $this->systemPermission = Permission::create([
        'permission_key' => 'manage-users',
        'description' => 'Manage system users and roles',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->adminRole->permissions()->attach($this->systemPermission->permission_id);

    // Create staff permission
    $this->staffPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->staffRole->permissions()->attach($this->staffPermission->permission_id);

    // Create an admin user to authenticate
    $this->adminUser = User::factory()->create([
        'username' => 'admin_test',
        'role_id' => $this->adminRole->role_id,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.permissions'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view the permissions management page', function (): void {
    $this->actingAs($this->adminUser)
        ->get(route('admin.permissions'))
        ->assertOk()
        ->assertSee('manage-users')
        ->assertSee('manage-reservations')
        ->assertSee('System')
        ->assertSee('Front Desk');
});

test('admin can create a new permission with valid data', function (): void {
    $permissionData = [
        'permission_key' => 'new-test-permission',
        'description' => 'A test permission',
        'module' => 'POS',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.permissions.store'), $permissionData);

    $response->assertRedirect(route('admin.permissions'));
    $response->assertSessionHas('success', 'Permission created successfully.');

    $this->assertDatabaseHas('permissions', [
        'permission_key' => 'new-test-permission',
        'description' => 'A test permission',
        'module' => 'POS',
        'is_active' => true,
    ]);
});

test('permission creation validation fails with invalid data', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.permissions.store'), [
            'permission_key' => 'invalid key spaces',
            'description' => '',
            'module' => 'InvalidModule',
        ]);

    $response->assertSessionHasErrors(['permission_key', 'description', 'module']);
});

test('admin can update a permission', function (): void {
    $updateData = [
        'permission_key' => 'updated-key',
        'description' => 'Updated Description',
        'module' => 'Accounting',
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.permissions.update', $this->staffPermission), $updateData);

    $response->assertRedirect(route('admin.permissions'));
    $response->assertSessionHas('success', 'Permission updated successfully.');

    $this->assertDatabaseHas('permissions', [
        'permission_id' => $this->staffPermission->permission_id,
        'permission_key' => 'updated-key',
        'description' => 'Updated Description',
        'module' => 'Accounting',
    ]);
});

test('admin can toggle active status of a permission', function (): void {
    // Toggle to inactive
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.permissions.toggle', $this->staffPermission));

    $response->assertRedirect(route('admin.permissions'));
    $response->assertSessionHas('success', 'Permission has been successfully disabled.');
    expect($this->staffPermission->fresh()->is_active)->toBeFalse();

    // Toggle back to active
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.permissions.toggle', $this->staffPermission));

    $response->assertRedirect(route('admin.permissions'));
    $response->assertSessionHas('success', 'Permission has been successfully enabled.');
    expect($this->staffPermission->fresh()->is_active)->toBeTrue();
});

test('admin cannot toggle system manage-users permission status', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.permissions.toggle', $this->systemPermission));

    $response->assertRedirect(route('admin.permissions'));
    $response->assertSessionHasErrors(['cannot_disable_manage_users']);
    expect($this->systemPermission->fresh()->is_active)->toBeTrue();
});

test('disabled permission is not considered active for users', function (): void {
    $staffUser = User::factory()->create([
        'role_id' => $this->staffRole->role_id,
    ]);

    // Initially active
    expect($staffUser->hasPermission('manage-reservations'))->toBeTrue();

    // Deactivate permission
    $this->staffPermission->update(['is_active' => false]);
    expect($staffUser->hasPermission('manage-reservations'))->toBeFalse();
});

test('disabled role deactivates all its permissions', function (): void {
    $staffUser = User::factory()->create([
        'role_id' => $this->staffRole->role_id,
    ]);

    // Initially active
    expect($staffUser->hasPermission('manage-reservations'))->toBeTrue();

    // Deactivate role
    $this->staffRole->update(['is_active' => false]);
    $staffUser->refresh();

    expect($staffUser->hasPermission('manage-reservations'))->toBeFalse();
});
