<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'create'])
    ->name('home');

Route::get('/login', [LoginController::class, 'create'])
    ->name('login');

Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::prefix('frontdesk')->group(function () {

        Route::view('/dashboard', 'frontdesk.dashboard.index')
            ->name('frontdesk.dashboard');

        Route::view('/reservation', 'frontdesk.reservation.index')
            ->name('frontdesk.reservation');

        Route::view('/registration', 'frontdesk.registration.index')
            ->name('frontdesk.registration');

        Route::view('/guest-list', 'frontdesk.guest-list.index')
            ->name('frontdesk.guest-list');

        Route::view('/guest-folio', 'frontdesk.guest-folio.index')
            ->name('frontdesk.guest-folio');

        Route::view('/shift-sales', 'frontdesk.shift-sales.index')
            ->name('frontdesk.shift-sales');

    });

    Route::prefix('accounting')->group(function () {

        Route::view('/dashboard', 'accounting.dashboard.index')
            ->name('accounting.dashboard');

        Route::view('/invoices', 'accounting.invoices.index')
            ->name('accounting.invoices');

        Route::view('/payments', 'accounting.payments.index')
            ->name('accounting.payments');

        Route::view('/reports', 'accounting.reports.index')
            ->name('accounting.reports');
    });

    Route::prefix('coffeeshop')->group(function () {

        Route::view('/dashboard', 'coffeeshop.dashboard.index')
            ->name('coffeeshop.dashboard');

        Route::view('/pos', 'coffeeshop.pos.index')
            ->name('coffeeshop.pos');

        Route::view('/orders', 'coffeeshop.orders.index')
            ->name('coffeeshop.orders');

        Route::view('/sales', 'coffeeshop.sales.index')
            ->name('coffeeshop.sales');

        Route::view('/inventory', 'coffeeshop.inventory.index')
            ->name('coffeeshop.inventory');
    });

    Route::prefix('admin')->group(function () {

        Route::view('/dashboard', 'admin.dashboard.index')
            ->name('admin.dashboard');

        Route::view('/users', 'admin.users.index')
            ->name('admin.users');

    Route::view('/roles', 'admin.roles.index')
        ->name('admin.roles');

    Route::view('/permissions', 'admin.permissions.index')
        ->name('admin.permissions');

    Route::view('/rooms', 'admin.rooms.index')
        ->name('admin.rooms');

    Route::view('/chargecodes', 'admin.charge-codes.index')
        ->name('admin.chargecodes');

    Route::view('/activitylogs', 'admin.activity-logs.index')
        ->name('admin.activitylogs');

});
});