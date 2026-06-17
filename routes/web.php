<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
});

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials)) {
        return back()
            ->withErrors([
                'username' => 'The provided credentials are incorrect.',
            ])
            ->onlyInput('username');
    }

    $request->session()->regenerate();

    return redirect('/');
});
