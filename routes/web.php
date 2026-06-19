<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ChargeCodeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ShiftScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontdesk\BookingOperationController;
use App\Http\Controllers\Frontdesk\DashboardController as FrontdeskDashboardController;
use App\Http\Controllers\Frontdesk\RegistrationController;
use App\Http\Controllers\Frontdesk\ReservationController;
use App\Http\Controllers\Frontdesk\ShiftController as FrontdeskShiftController;
use App\Http\Controllers\Frontdesk\ShiftSalesController;
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

        Route::get('/dashboard', [FrontdeskDashboardController::class, 'index'])
            ->name('frontdesk.dashboard');

        Route::post('/booking/check-in', [BookingOperationController::class, 'checkIn'])
            ->name('frontdesk.booking.check-in');

        Route::post('/booking/check-out', [BookingOperationController::class, 'checkOut'])
            ->name('frontdesk.booking.check-out');

        Route::post('/room/mark-cleaned', [BookingOperationController::class, 'markCleaned'])
            ->name('frontdesk.room.mark-cleaned');

        Route::post('/room/mark-for-cleaning', [BookingOperationController::class, 'markForCleaning'])
            ->name('frontdesk.room.mark-for-cleaning');

        Route::post('/room/mark-maintenance', [BookingOperationController::class, 'markForMaintenance'])
            ->name('frontdesk.room.mark-maintenance');

        Route::post('/room/maintenance-complete', [BookingOperationController::class, 'markMaintenanceComplete'])
            ->name('frontdesk.room.maintenance-complete');

        Route::get('/reservation', [ReservationController::class, 'index'])
            ->name('frontdesk.reservation');

        Route::post('/reservation', [ReservationController::class, 'store'])
            ->name('frontdesk.reservation.store');

        Route::patch('/reservation/{booking}/cancel', [ReservationController::class, 'cancel'])
            ->name('frontdesk.reservation.cancel');

        Route::get('/registration', [RegistrationController::class, 'index'])
            ->name('frontdesk.registration');

        Route::post('/registration', [RegistrationController::class, 'store'])
            ->name('frontdesk.registration.store');

        Route::view('/guest-list', 'frontdesk.guest-list.index')
            ->name('frontdesk.guest-list');

        Route::view('/guest-folio', 'frontdesk.guest-folio.index')
            ->name('frontdesk.guest-folio');

        Route::get('/shift-sales', [ShiftSalesController::class, 'index'])
            ->name('frontdesk.shift-sales');

        Route::post('/shift/open', [FrontdeskShiftController::class, 'open'])
            ->name('frontdesk.shift.open');

        Route::post('/shift/close', [FrontdeskShiftController::class, 'close'])
            ->name('frontdesk.shift.close');

    });

    Route::prefix('accounting')->group(function () {

        Route::view('/dashboard', 'accounting.dashboard.index')
            ->name('accounting.dashboard');

        Route::view('/billing', 'accounting.billing.index')
            ->name('accounting.billing');

        Route::view('/payments', 'accounting.payments.index')
            ->name('accounting.payments');

        Route::view('/receivables', 'accounting.receivables.index')
            ->name('accounting.receivables');

        Route::view('/expenses', 'accounting.expenses.index')
            ->name('accounting.expenses');

        Route::view('/reports', 'accounting.reports.index')
            ->name('accounting.reports');

        Route::view('/audit', 'accounting.audit.index')
            ->name('accounting.audit');

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

        Route::get('/rooms', [RoomController::class, 'index'])
            ->name('admin.rooms');

        Route::post('/rooms', [RoomController::class, 'store'])
            ->name('admin.rooms.store');

        Route::patch('/rooms/{room}/toggle', [RoomController::class, 'toggleStatus'])
            ->name('admin.rooms.toggle');

        Route::patch('/rooms/{room}', [RoomController::class, 'update'])
            ->name('admin.rooms.update');

        Route::get('/chargecodes', [ChargeCodeController::class, 'index'])
            ->name('admin.chargecodes');

        Route::post('/chargecodes', [ChargeCodeController::class, 'store'])
            ->name('admin.chargecodes.store');

        Route::patch('/chargecodes/{chargeCode}/toggle', [ChargeCodeController::class, 'toggleStatus'])
            ->name('admin.chargecodes.toggle');

        Route::patch('/chargecodes/{chargeCode}', [ChargeCodeController::class, 'update'])
            ->name('admin.chargecodes.update');

        Route::get('/activitylogs', [ActivityLogController::class, 'index'])
            ->name('admin.activitylogs');

        Route::get('/activitylogs/export', [ActivityLogController::class, 'export'])
            ->name('admin.activitylogs.export');

        // SHIFT SCHEDULES
        Route::get('/shift-schedules', [ShiftScheduleController::class, 'index'])
            ->name('admin.shift-schedules');

        Route::post('/shift-schedules', [ShiftScheduleController::class, 'store'])
            ->name('admin.shift-schedules.store');

        Route::patch('/shift-schedules/{schedule}', [ShiftScheduleController::class, 'update'])
            ->name('admin.shift-schedules.update');

        Route::delete('/shift-schedules/{schedule}', [ShiftScheduleController::class, 'destroy'])
            ->name('admin.shift-schedules.delete');

        Route::get('/shift-schedules/{schedule}/report', [ShiftScheduleController::class, 'report'])
            ->name('admin.shift-schedules.report');

        Route::get('/shift-sales', [ShiftSalesController::class, 'index'])
            ->name('admin.shift-sales');

    });
});
