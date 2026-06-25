<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // 1. Create a base role with NO permissions
    $this->limitedRole = Role::create([
        'role_name' => 'LIMITED_STAFF',
        'description' => 'Staff with no role-based permissions',
        'is_active' => true,
        'is_system_admin' => false,
    ]);

    // 2. Create permissions
    $this->checkInPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->viewFolioPermission = Permission::create([
        'permission_key' => 'view-guest-folio',
        'description' => 'View guest folio',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    // 3. Create a test user assigned to this limited role
    $this->testUser = User::factory()->create([
        'username' => 'limited_user',
        'role_id' => $this->limitedRole->role_id,
        'is_active' => true,
    ]);
});

test('user without permissions is denied access', function (): void {
    expect($this->testUser->hasPermission('manage-reservations'))->toBeFalse();
    expect($this->testUser->hasPermission('view-guest-folio'))->toBeFalse();
});

test('user can get access via role permissions', function (): void {
    // Sync view-guest-folio permission to the role
    $this->limitedRole->permissions()->sync([
        $this->viewFolioPermission->permission_id,
    ]);

    expect($this->testUser->hasPermission('view-guest-folio'))->toBeTrue();
    expect($this->testUser->hasPermission('manage-reservations'))->toBeFalse();
});

test('user can get access via direct permission overrides', function (): void {
    // Assign manage-reservations directly to the user
    $this->testUser->permissions()->sync([
        $this->checkInPermission->permission_id,
    ]);

    expect($this->testUser->hasPermission('manage-reservations'))->toBeTrue();
    expect($this->testUser->hasPermission('view-guest-folio'))->toBeFalse();
});

test('system admin bypasses all permission checks', function (): void {
    // Create admin role
    $adminRole = Role::create([
        'role_name' => 'SUPER_ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
        'is_system_admin' => true,
    ]);

    $adminUser = User::factory()->create([
        'username' => 'admin_user',
        'role_id' => $adminRole->role_id,
        'is_active' => true,
    ]);

    expect($adminUser->hasPermission('manage-reservations'))->toBeTrue();
    expect($adminUser->hasPermission('view-guest-folio'))->toBeTrue();
});
