<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('redirects users to the correct dashboard after login', function (string $role, string $routeName): void {
    $user = User::factory()->create([
        'username' => strtolower(str_replace('_', '', $role)),
        'password_hash' => Hash::make('password'),
        'role' => $role,
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
