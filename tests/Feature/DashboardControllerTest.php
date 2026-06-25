<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Setup initial Roles
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $this->inactiveRole = Role::create([
        'role_name' => 'TEST_ROLE',
        'description' => 'Test Inactive Role',
        'is_active' => false,
    ]);

    // Setup initial Permissions
    $this->manageUsersPermission = Permission::create([
        'permission_key' => 'manage-users',
        'description' => 'Manage users',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->reservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->inactivePermission = Permission::create([
        'permission_key' => 'some-disabled-feature',
        'description' => 'Disabled feature',
        'module' => 'System',
        'is_active' => false,
    ]);

    // Sync roles and permissions
    $this->adminRole->permissions()->sync([
        $this->manageUsersPermission->permission_id,
        $this->reservationsPermission->permission_id,
    ]);

    $this->staffRole->permissions()->sync([
        $this->reservationsPermission->permission_id,
    ]);

    // Create users
    $this->adminUser = User::factory()->create([
        'username' => 'admin_test',
        'full_name' => 'Admin User',
        'role_id' => $this->adminRole->role_id,
        'is_active' => true,
    ]);

    $this->staffUser = User::factory()->create([
        'username' => 'staff_test',
        'full_name' => 'Front Desk Person',
        'role_id' => $this->staffRole->role_id,
        'is_active' => true,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view the admin dashboard page with dynamic data', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.dashboard'));

    $response->assertOk();

    // Check KPI counts (Total roles: 3, Total permissions: 3, Total users: 2)
    $response->assertSee('3'); // Total Roles count (admin, staff, inactive)
    $response->assertSee('2'); // Total Users count

    // Check roles, permissions, and users are rendered
    $response->assertSee('Admin');
    $response->assertSee('Front Desk');
    $response->assertSee('Test Role');

    $response->assertSee('manage-users');
    $response->assertSee('manage-reservations');
    $response->assertSee('some-disabled-feature');

    $response->assertSee('Admin User');
    $response->assertSee('Front Desk Person');
});

test('sidebar renders dynamic links based on user permissions for staff user', function (): void {
    // staffUser has only 'manage-reservations' permission
    $response = $this->actingAs($this->staffUser)
        ->get(route('frontdesk.dashboard'));

    $response->assertOk();

    // Should see reservation links
    $response->assertSee(route('frontdesk.dashboard'));
    $response->assertSee(route('frontdesk.reservation'));
    $response->assertSee(route('frontdesk.registration'));

    // Should NOT see view-folio links (guest list, guest folio, shift sales)
    $response->assertDontSee(route('frontdesk.guest-list'));
    $response->assertDontSee(route('frontdesk.guest-folio'));
    $response->assertDontSee(route('frontdesk.shift-sales'));
});

test('sidebar renders dynamic links for user with custom multiple permissions', function (): void {
    // Create custom role and assign multiple permissions
    $customRole = Role::create([
        'role_name' => 'CUSTOM_STAFF',
        'description' => 'Custom Staff Role',
        'is_active' => true,
    ]);

    $viewGuestList = Permission::create([
        'permission_key' => 'view-guest-list',
        'description' => 'View guest list',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $viewGuestFolio = Permission::create([
        'permission_key' => 'view-guest-folio',
        'description' => 'View guest folio',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $viewShiftSales = Permission::create([
        'permission_key' => 'view-shift-sales',
        'description' => 'View shift sales',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $customRole->permissions()->sync([
        $this->reservationsPermission->permission_id,
        $viewGuestList->permission_id,
        $viewGuestFolio->permission_id,
        $viewShiftSales->permission_id,
    ]);

    $customUser = User::factory()->create([
        'username' => 'custom_staff',
        'full_name' => 'Custom Staff Person',
        'role_id' => $customRole->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($customUser)
        ->get(route('frontdesk.dashboard'));

    $response->assertOk();

    // Should see reservations links
    $response->assertSee(route('frontdesk.dashboard'));
    $response->assertSee(route('frontdesk.reservation'));
    $response->assertSee(route('frontdesk.registration'));

    // Should also see view-folio links now
    $response->assertSee(route('frontdesk.guest-list'));
    $response->assertSee(route('frontdesk.guest-folio'));
    $response->assertSee(route('frontdesk.shift-sales'));
});
