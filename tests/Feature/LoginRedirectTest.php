<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('redirects users to the correct dashboard after login', function (string $role, string $routeName): void {
    $roleModel = Role::create([
        'role_name' => $role,
        'description' => 'Test Role',
    ]);

    $user = User::factory()->create([
        'username' => strtolower(str_replace('_', '', $role)).'_test',
        'password_hash' => Hash::make('password'),
        'role_id' => $roleModel->role_id,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response->assertRedirect(route($routeName));
    $response->assertSessionHas('show_login_confirmation', true);

    $this->assertAuthenticatedAs($user);
})->with([
    ['ADMIN', 'admin.dashboard'],
    ['FRONT_DESK', 'frontdesk.dashboard'],
    ['FRONTDESK', 'frontdesk.dashboard'],
    ['ACCOUNTING', 'accounting.dashboard'],
    ['CAFETERIA', 'coffeeshop.dashboard'],
]);

it('redirects custom created roles based on permissions', function (string $permissionKey, string $routeName): void {
    $permission = Permission::firstOrCreate(
        ['permission_key' => $permissionKey],
        [
            'permission_name' => 'Test Permission',
            'description' => 'Test Permission Description',
            'module' => 'Test',
            'is_active' => true,
        ]
    );

    $roleModel = Role::create([
        'role_name' => 'CUSTOM_ROLE_'.rand(100, 999),
        'description' => 'Custom Role',
    ]);
    $roleModel->permissions()->attach($permission->permission_id);

    $user = User::factory()->create([
        'username' => 'custom_user_'.rand(100, 999),
        'password_hash' => Hash::make('password'),
        'role_id' => $roleModel->role_id,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response->assertRedirect(route($routeName));
    $response->assertSessionHas('show_login_confirmation', true);
    $this->assertAuthenticatedAs($user);
})->with([
    ['manage-reservations', 'frontdesk.dashboard'],
    ['view-accounting-dashboard', 'accounting.dashboard'],
    ['manage-inventory', 'coffeeshop.dashboard'],
    ['manage-users', 'admin.dashboard'],
]);
