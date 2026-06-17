<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard.index')->name('dashboard');
Route::view('/reservation', 'reservation.index')->name('reservation');
Route::view('/registration', 'registration.index')->name('registration');
Route::view('/guest-folio', 'guest-folio.index')->name('guest-folio');
Route::view('/shift-sales', 'shift-sales.index')->name('shift-sales');
Route::view('/guest-list', 'guest-list.index')->name('guest-list');
