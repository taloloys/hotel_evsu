<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/login', [LoginController::class, 'create'])->name('login');

Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function (): void {
	Route::view('/dashboard', 'dashboard.index')->name('dashboard');
	Route::view('/reservation', 'reservation.index')->name('reservation');
	Route::view('/registration', 'registration.index')->name('registration');
	Route::view('/guest-list', 'guest-list.index')->name('guest-list');
	Route::view('/guest-folio', 'guest-folio.index')->name('guest-folio');
	Route::view('/shift-sales', 'shift-sales.index')->name('shift-sales');
});
