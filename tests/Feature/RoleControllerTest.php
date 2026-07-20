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
    $this->get(route('admin.roles'))
        ->assertRedirect(route('home'));
});

test('authenticated admin can view the roles management page with permissions', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.roles'))
        ->assertOk()
        ->assertSee('Admin Role')
        ->assertSee('Staff Role')
        ->assertSee('manage-users')
        ->assertSee('manage-reservations');
});

test('admin can create a new role and sync permissions', function (): void {
    $roleData = [
        'role_name' => 'NEW_ROLE',
        'description' => 'A new test role',
        'permissions' => [
            $this->staffPermission->permission_id,
        ],
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.roles.store'), $roleData);

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHas('success', 'Role created successfully.');

    $this->assertDatabaseHas('roles', [
        'role_name' => 'NEW_ROLE',
        'description' => 'A new test role',
        'is_active' => true,
    ]);

    $newRole = Role::where('role_name', 'NEW_ROLE')->first();
    expect($newRole->permissions->pluck('permission_id')->toArray())
        ->toContain($this->staffPermission->permission_id);
});

test('admin can update a role and sync permissions', function (): void {
    $updateData = [
        'role_name' => 'UPDATED_ROLE',
        'description' => 'Updated description',
        'permissions' => [
            $this->systemPermission->permission_id,
        ],
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.roles.update', $this->staffRole), $updateData);

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHas('success', 'Role updated successfully.');

    $this->assertDatabaseHas('roles', [
        'role_id' => $this->staffRole->role_id,
        'role_name' => 'UPDATED_ROLE',
        'description' => 'Updated description',
    ]);

    expect($this->staffRole->fresh()->permissions->pluck('permission_id')->toArray())
        ->toContain($this->systemPermission->permission_id)
        ->not->toContain($this->staffPermission->permission_id);
});

test('admin cannot update the system ADMIN role', function (): void {
    $updateData = [
        'role_name' => 'ATTEMPTED_ADMIN_UPDATE',
        'description' => 'Attempted description',
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.roles.update', $this->adminRole), $updateData);

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHasErrors(['cannot_edit_admin']);

    $this->assertDatabaseHas('roles', [
        'role_id' => $this->adminRole->role_id,
        'role_name' => 'ADMIN',
    ]);
});

test('admin cannot toggle status of the system ADMIN role', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.roles.toggle', $this->adminRole));

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHasErrors(['cannot_disable_admin']);

    expect($this->adminRole->fresh()->is_active)->toBeTrue();
});

test('admin can toggle status of other roles', function (): void {
    // Disable role
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.roles.toggle', $this->staffRole));

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHas('success', 'Role has been successfully disabled.');

    expect($this->staffRole->fresh()->is_active)->toBeFalse();

    // Enable role
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.roles.toggle', $this->staffRole));

    $response->assertRedirect(route('admin.roles'));
    $response->assertSessionHas('success', 'Role has been successfully enabled.');

    expect($this->staffRole->fresh()->is_active)->toBeTrue();
});
