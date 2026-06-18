<?php

use Illuminate\Support\Facades\Route;

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

    // CHARGE CODES (FIXED naming consistency)
    Route::view('/chargecodes', 'admin.chargecodes.index')
        ->name('admin.chargecodes');

    // ACTIVITY LOGS (KEEP THIS CONSISTENT)
    Route::view('/activitylogs', 'admin.activitylogs.index')
        ->name('admin.activitylogs');

});