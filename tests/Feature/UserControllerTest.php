<?php

use App\Mail\UserAccountCreatedMail;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Set up test roles
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    $manageUsersPerm = Permission::create([
        'permission_key' => 'manage-users',
        'description' => 'Manage Users',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->adminRole->permissions()->attach($manageUsersPerm->permission_id);

    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Staff Role',
        'is_active' => true,
    ]);

    // Create an admin user to authenticate
    $this->adminUser = User::factory()->create([
        'username' => 'admin_test',
        'role_id' => $this->adminRole->role_id,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.users'))
        ->assertRedirect(route('home'));
});

test('non-admin users without permission cannot view the users management page', function (): void {
    $staffUser = User::factory()->create([
        'username' => 'staff_no_perm',
        'role_id' => $this->staffRole->role_id,
    ]);

    $this->actingAs($staffUser)
        ->get(route('admin.users'))
        ->assertForbidden();
});

test('authenticated admin can view the users management page', function (): void {
    // Create another user to display
    $staffUser = User::factory()->create([
        'username' => 'staff_member',
        'full_name' => 'John Doe',
        'role_id' => $this->staffRole->role_id,
    ]);

    $this->actingAs($this->adminUser)
        ->get(route('admin.users'))
        ->assertOk()
        ->assertSee('John Doe')
        ->assertSee('staff_member')
        ->assertSee('Admin')
        ->assertSee('Front Desk');
});

test('admin can create a new user with valid data', function (): void {
    Mail::fake();

    $userData = [
        'full_name' => 'Jane Smith',
        'username' => 'janesmith',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'role_id' => $this->staffRole->role_id,
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.store'), $userData);

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHas('success', 'User created successfully.');

    $this->assertDatabaseHas('users', [
        'username' => 'janesmith',
        'full_name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'role_id' => $this->staffRole->role_id,
    ]);

    $createdUser = User::where('username', 'janesmith')->first();
    expect(Hash::check('secret123', $createdUser->password_hash))->toBeTrue();

    Mail::assertQueued(UserAccountCreatedMail::class, function ($mail) use ($createdUser) {
        return $mail->hasTo('jane@example.com') &&
               $mail->user->user_id === $createdUser->user_id &&
               $mail->plainPassword === 'secret123';
    });
});

test('user creation validation fails with missing data', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.store'), [
            'full_name' => '',
            'username' => '',
            'password' => '',
            'role_id' => 9999, // Non-existent role ID
        ]);

    $response->assertSessionHasErrors(['full_name', 'username', 'password', 'role_id']);
});

test('user creation validation fails if username is not unique', function (): void {
    // Create existing user
    User::factory()->create([
        'username' => 'duplicate_user',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.store'), [
            'full_name' => 'Some Name',
            'username' => 'duplicate_user',
            'password' => 'password123',
            'role_id' => $this->staffRole->role_id,
        ]);

    $response->assertSessionHasErrors(['username']);
});

test('admin can toggle active status of a user', function (): void {
    $staffUser = User::factory()->create([
        'username' => 'staff_toggle',
        'is_active' => true,
        'role_id' => $this->staffRole->role_id,
    ]);

    // Toggle to inactive
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.users.toggle', $staffUser));

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHas('success', 'User account has been successfully disabled.');
    expect($staffUser->fresh()->is_active)->toBeFalse();

    // Toggle back to active
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.users.toggle', $staffUser));

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHas('success', 'User account has been successfully enabled.');
    expect($staffUser->fresh()->is_active)->toBeTrue();
});

test('admin cannot toggle their own active status', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.users.toggle', $this->adminUser));

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHasErrors(['cannot_disable_self']);
    expect($this->adminUser->fresh()->is_active)->toBeTrue();
});

test('disabled user cannot login', function (): void {
    $disabledUser = User::factory()->create([
        'username' => 'disabled_staff',
        'password_hash' => Hash::make('password123'),
        'is_active' => false,
        'role_id' => $this->staffRole->role_id,
    ]);

    $response = $this->post('/login', [
        'username' => 'disabled_staff',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['username']);
    $this->assertGuest();
});

test('admin can create a new user with direct permissions', function (): void {
    $permission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Test',
        'module' => 'Test',
        'is_active' => true,
    ]);

    $userData = [
        'full_name' => 'Jane Smith',
        'username' => 'janesmith',
        'password' => 'secret123',
        'role_id' => $this->staffRole->role_id,
        'permissions' => [$permission->permission_id],
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.store'), $userData);

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHas('success', 'User created successfully.');

    $createdUser = User::where('username', 'janesmith')->first();
    expect($createdUser->permissions)->toHaveCount(1);
    expect($createdUser->permissions->first()->permission_id)->toBe($permission->permission_id);
});

test('admin can update a user and sync direct permissions', function (): void {
    $permission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Test',
        'module' => 'Test',
        'is_active' => true,
    ]);

    $staffUser = User::factory()->create([
        'username' => 'staff_update_test',
        'role_id' => $this->staffRole->role_id,
    ]);

    $updateData = [
        'full_name' => 'Jane Updated',
        'username' => 'staff_update_test',
        'role_id' => $this->staffRole->role_id,
        'permissions' => [$permission->permission_id],
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.users.update', $staffUser), $updateData);

    $response->assertRedirect(route('admin.users'));
    $response->assertSessionHas('success', 'User updated successfully.');

    expect($staffUser->fresh()->permissions)->toHaveCount(1);
    expect($staffUser->fresh()->permissions->first()->permission_id)->toBe($permission->permission_id);
});
