<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
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

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.users');

        Route::post('/users', [UserController::class, 'store'])
            ->name('admin.users.store');

        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])
            ->name('admin.users.toggle');

        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->name('admin.users.update');

        Route::get('/roles', [RoleController::class, 'index'])
            ->name('admin.roles');

        Route::post('/roles', [RoleController::class, 'store'])
            ->name('admin.roles.store');

        Route::patch('/roles/{role}/toggle', [RoleController::class, 'toggleStatus'])
            ->name('admin.roles.toggle');

        Route::patch('/roles/{role}', [RoleController::class, 'update'])
            ->name('admin.roles.update');

        Route::get('/permissions', [PermissionController::class, 'index'])
            ->name('admin.permissions');

        Route::post('/permissions', [PermissionController::class, 'store'])
            ->name('admin.permissions.store');

        Route::patch('/permissions/{permission}/toggle', [PermissionController::class, 'toggleStatus'])
            ->name('admin.permissions.toggle');

        Route::patch('/permissions/{permission}', [PermissionController::class, 'update'])
            ->name('admin.permissions.update');

        Route::view('/rooms', 'admin.rooms.index')
            ->name('admin.rooms');

        Route::view('/chargecodes', 'admin.chargecodes.index')
            ->name('admin.chargecodes');

        Route::view('/activitylogs', 'admin.activitylogs.index')
            ->name('admin.activitylogs');

    });
});
