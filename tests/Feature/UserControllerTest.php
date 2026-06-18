<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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

    // Create an admin user to authenticate
    $this->adminUser = User::factory()->create([
        'username' => 'admin_test',
        'role_id' => $this->adminRole->role_id,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.users'))
        ->assertRedirect(route('login'));
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
    $userData = [
        'full_name' => 'Jane Smith',
        'username' => 'janesmith',
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
        'role_id' => $this->staffRole->role_id,
    ]);

    $createdUser = User::where('username', 'janesmith')->first();
    expect(Hash::check('secret123', $createdUser->password_hash))->toBeTrue();
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
