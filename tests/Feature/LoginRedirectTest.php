<?php

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

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ])->assertRedirect(route($routeName));

    $this->assertAuthenticatedAs($user);
})->with([
    ['ADMIN', 'admin.dashboard'],
    ['FRONT_DESK', 'frontdesk.dashboard'],
    ['ACCOUNTING', 'accounting.dashboard'],
    ['CAFETERIA', 'coffeeshop.dashboard'],
]);
