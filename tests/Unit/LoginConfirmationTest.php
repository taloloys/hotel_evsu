<?php

use App\Http\Controllers\Auth\LoginController;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class);

it('sets a login confirmation flash after successful authentication', function () {
    Auth::shouldReceive('attempt')
        ->once()
        ->with(['username' => 'frontdesk_test', 'password' => 'password', 'is_active' => true], false)
        ->andReturn(true);

    Auth::shouldReceive('user')
        ->once()
        ->andReturn((object) [
            'username' => 'frontdesk_test',
            'role' => (object) ['role_name' => 'FRONT_DESK'],
        ]);

    Mockery::mock('alias:App\Models\ActivityLog')
        ->shouldReceive('log')
        ->once()
        ->with('LOGIN', 'User logged in successfully.');

    Session::start();

    $request = Request::create('/login', 'POST', [
        'username' => 'frontdesk_test',
        'password' => 'password',
    ]);

    $request->setLaravelSession(app('session.store'));

    $response = (new LoginController())->store($request);

    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    expect($response->getSession()->get('show_login_confirmation'))->toBeTrue();
});
