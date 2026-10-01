<?php

use App\Http\Controllers\Auth\LoginController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('sets a login confirmation flash after successful authentication', function () {
    Auth::shouldReceive('attempt')
        ->once()
        ->with(['username' => 'frontdesk_test', 'password' => 'password', 'is_active' => true], false)
        ->andReturn(true);

    $authenticatedUser = new User([
        'username' => 'frontdesk_test',
    ]);
    $authenticatedUser->setRelation('role', new Role([
        'role_name' => 'FRONT_DESK',
    ]));

    Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front desk role',
        'is_active' => true,
    ]);
    User::factory()->create();

    Auth::shouldReceive('user')
        ->once()
        ->andReturn($authenticatedUser);
    Auth::shouldReceive('id')
        ->once()
        ->andReturn(null);

    Session::start();

    $request = Request::create('/login', 'POST', [
        'username' => 'frontdesk_test',
        'password' => 'password',
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = (new LoginController)->store($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    expect($response->getSession()->get('show_login_confirmation'))->toBeTrue();
});
