<?php

use App\Http\Controllers\Accounting\AuditController;
use App\Http\Controllers\Accounting\BillingController;
use App\Http\Controllers\Accounting\ExpenseController;
use App\Http\Controllers\Accounting\PaymentController;
use App\Http\Controllers\Accounting\ReceivableController;
use App\Http\Controllers\Accounting\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ChargeCodeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ShiftScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Coffeeshop\CustomerController as CoffeeshopCustomerController;
use App\Http\Controllers\Coffeeshop\DashboardController as CoffeeshopDashboardController;
use App\Http\Controllers\Coffeeshop\InventoryController as CoffeeshopInventoryController;
use App\Http\Controllers\Coffeeshop\OrderController as CoffeeshopOrderController;
use App\Http\Controllers\Coffeeshop\PosController as CoffeeshopPosController;
use App\Http\Controllers\Coffeeshop\ProductController as CoffeeshopProductController;
use App\Http\Controllers\Coffeeshop\ReportController as CoffeeshopReportController;
use App\Http\Controllers\Coffeeshop\SettingsController as CoffeeshopSettingsController;
use App\Http\Controllers\Coffeeshop\StatisticsController as CoffeeshopStatisticsController;
use App\Http\Controllers\Coffeeshop\TabController as CoffeeshopTabController;
use App\Http\Controllers\Frontdesk\BookingOperationController;
use App\Http\Controllers\Frontdesk\CheckInController;
use App\Http\Controllers\Frontdesk\DashboardController as FrontdeskDashboardController;
use App\Http\Controllers\Frontdesk\GuestFolioController;
use App\Http\Controllers\Frontdesk\GuestListController;
use App\Http\Controllers\Frontdesk\RegistrationController;
use App\Http\Controllers\Frontdesk\ReservationController;
use App\Http\Controllers\Frontdesk\ShiftController as FrontdeskShiftController;
use App\Http\Controllers\Frontdesk\ShiftSalesController;
use App\Http\Controllers\LayoutDataController;
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

    Route::get('/api/layout-data', [LayoutDataController::class, 'getLayoutData'])
        ->name('api.layout-data');

    Route::prefix('frontdesk')->group(function () {

        Route::middleware('can:manage-reservations')->group(function () {
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

            Route::get('/guests/search', [GuestListController::class, 'searchJson'])
                ->name('frontdesk.guests.search');

            Route::get('/check-in', [CheckInController::class, 'index'])
                ->name('frontdesk.checkin');

            Route::post('/check-in', [CheckInController::class, 'store'])
                ->name('frontdesk.checkin.store');

            Route::post('/shift/open', [FrontdeskShiftController::class, 'open'])
                ->name('frontdesk.shift.open');

            Route::post('/shift/close', [FrontdeskShiftController::class, 'close'])
                ->name('frontdesk.shift.close');
        });

        Route::middleware('can:view-guest-list')->group(function () {
            Route::get('/guest-list', [GuestListController::class, 'index'])
                ->name('frontdesk.guest-list');
        });

        Route::middleware('can:view-guest-folio')->group(function () {
            Route::get('/guest-folio', [GuestFolioController::class, 'index'])
                ->name('frontdesk.guest-folio');

            Route::middleware('can:manage-guest-folio')->group(function () {
                Route::post('/guest-folio/{folio}/transaction', [GuestFolioController::class, 'postTransaction'])
                    ->name('frontdesk.guest-folio.transaction');

                Route::post('/guest-folio/booking/{booking}/transfer', [GuestFolioController::class, 'transferRoom'])
                    ->name('frontdesk.guest-folio.transfer');

                Route::post('/guest-folio/booking/{booking}/check-in', [GuestFolioController::class, 'checkInBooking'])
                    ->name('frontdesk.guest-folio.checkin');

                Route::post('/guest-folio/booking/{booking}/check-out', [GuestFolioController::class, 'checkOutBooking'])
                    ->name('frontdesk.guest-folio.checkout');

                Route::post('/guest-folio/{folio}/close', [GuestFolioController::class, 'closeFolio'])
                    ->name('frontdesk.guest-folio.close');

                Route::post('/guest-folio/{folio}/reopen', [GuestFolioController::class, 'reopenFolio'])
                    ->name('frontdesk.guest-folio.reopen');
            });
        });

        Route::middleware('can:view-shift-sales')->group(function () {
            Route::get('/shift-sales', [ShiftSalesController::class, 'index'])
                ->name('frontdesk.shift-sales');

            Route::get('/shift-sales/{shift}', [ShiftSalesController::class, 'show'])
                ->name('frontdesk.shift-sales.show');
        });

    });

    Route::prefix('accounting')->group(function () {

        Route::middleware('can:view-accounting-dashboard')->get('/dashboard', [App\Http\Controllers\Accounting\DashboardController::class, 'index'])
            ->name('accounting.dashboard');

        Route::middleware('can:manage-accounting-billing')->group(function () {
            Route::get('/billing', [BillingController::class, 'index'])
                ->name('accounting.billing');

            Route::get('/billing/{folio}', [BillingController::class, 'show'])
                ->name('accounting.billing.show');
        });

        Route::middleware('can:manage-accounting-payments')->group(function () {
            Route::get('/payments', [PaymentController::class, 'index'])
                ->name('accounting.payments');

            Route::post('/payments', [PaymentController::class, 'store'])
                ->name('accounting.payments.store');
        });

        Route::middleware('can:manage-accounting-receivables')->get('/receivables', [ReceivableController::class, 'index'])
            ->name('accounting.receivables');

        Route::middleware('can:manage-accounting-expenses')->group(function () {
            Route::get('/expenses', [ExpenseController::class, 'index'])
                ->name('accounting.expenses');

            Route::post('/expenses', [ExpenseController::class, 'store'])
                ->name('accounting.expenses.store');

            Route::patch('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])
                ->name('accounting.expenses.approve');
        });

        Route::middleware('can:view-accounting-reports')->get('/reports', [ReportController::class, 'index'])
            ->name('accounting.reports');

        Route::middleware('can:view-accounting-audit')->get('/audit', [AuditController::class, 'index'])
            ->name('accounting.audit');

    });

    Route::prefix('coffeeshop')->middleware('can:manage-inventory')->group(function () {

        Route::get('/dashboard', [CoffeeshopDashboardController::class, 'index'])
            ->name('coffeeshop.dashboard');

        Route::get('/pos', [CoffeeshopPosController::class, 'index'])
            ->name('coffeeshop.pos');

        Route::get('/products', [CoffeeshopProductController::class, 'index'])
            ->name('coffeeshop.products');
        Route::get('/products/create', [CoffeeshopProductController::class, 'create'])
            ->name('coffeeshop.products.create');
        Route::post('/products', [CoffeeshopProductController::class, 'store'])
            ->name('coffeeshop.products.store');
        Route::get('/products/{product}/edit', [CoffeeshopProductController::class, 'edit'])
            ->name('coffeeshop.products.edit');
        Route::put('/products/{product}', [CoffeeshopProductController::class, 'update'])
            ->name('coffeeshop.products.update');
        Route::delete('/products/{product}', [CoffeeshopProductController::class, 'destroy'])
            ->name('coffeeshop.products.destroy');

        Route::get('/inventory', [CoffeeshopInventoryController::class, 'index'])
            ->name('coffeeshop.inventory');
        Route::post('/inventory/{product}/adjust', [CoffeeshopInventoryController::class, 'adjust'])
            ->name('coffeeshop.inventory.adjust');

        Route::get('/tabs', [CoffeeshopTabController::class, 'index'])
            ->name('coffeeshop.tabs');
        Route::post('/tabs/{tab}/reopen', [CoffeeshopTabController::class, 'reopen'])
            ->name('coffeeshop.tabs.reopen');

        Route::get('/orders', [CoffeeshopOrderController::class, 'index'])
            ->name('coffeeshop.orders');
        Route::get('/orders/{order}', [CoffeeshopOrderController::class, 'show'])
            ->name('coffeeshop.orders.show');
        Route::post('/orders/{order}/refund', [CoffeeshopOrderController::class, 'refund'])
            ->name('coffeeshop.orders.refund');
        Route::post('/orders/{order}/cancel', [CoffeeshopOrderController::class, 'cancel'])
            ->name('coffeeshop.orders.cancel');

        Route::get('/customers', [CoffeeshopCustomerController::class, 'index'])
            ->name('coffeeshop.customers');

        Route::get('/statistics', [CoffeeshopStatisticsController::class, 'index'])
            ->name('coffeeshop.statistics');

        Route::get('/reports', [CoffeeshopReportController::class, 'index'])
            ->name('coffeeshop.reports');
        Route::get('/reports/export', [CoffeeshopReportController::class, 'export'])
            ->name('coffeeshop.reports.export');
        Route::redirect('/sales', '/coffeeshop/reports')
            ->name('coffeeshop.sales');

        Route::get('/settings', [CoffeeshopSettingsController::class, 'index'])
            ->name('coffeeshop.settings');
        Route::put('/settings', [CoffeeshopSettingsController::class, 'update'])
            ->name('coffeeshop.settings.update');
        Route::post('/settings/categories', [CoffeeshopSettingsController::class, 'storeCategory'])
            ->name('coffeeshop.settings.categories.store');
        Route::patch('/settings/categories/{category}/toggle', [CoffeeshopSettingsController::class, 'toggleCategory'])
            ->name('coffeeshop.settings.categories.toggle');

        Route::prefix('api')->name('coffeeshop.api.')->group(function () {
            Route::get('/products/search', [CoffeeshopPosController::class, 'searchProducts'])
                ->name('products.search');
            Route::get('/guests/checked-in', [CoffeeshopPosController::class, 'checkedInGuests'])
                ->name('guests.checked-in');
            Route::get('/tabs', [CoffeeshopPosController::class, 'listTabs'])
                ->name('tabs.index');
            Route::post('/tabs', [CoffeeshopPosController::class, 'storeTab'])
                ->name('tabs.store');
            Route::post('/tabs/{tab}/items', [CoffeeshopPosController::class, 'addTabItem'])
                ->name('tabs.items.store');
            Route::patch('/tabs/{tab}/items/{item}', [CoffeeshopPosController::class, 'updateTabItem'])
                ->name('tabs.items.update');
            Route::delete('/tabs/{tab}/items/{item}', [CoffeeshopPosController::class, 'removeTabItem'])
                ->name('tabs.items.destroy');
            Route::post('/tabs/{tab}/close', [CoffeeshopPosController::class, 'closeTab'])
                ->name('tabs.close');
            Route::post('/tabs/{tab}/cancel', [CoffeeshopPosController::class, 'cancelTab'])
                ->name('tabs.cancel');
        });
    });

    Route::prefix('admin')->group(function () {
        Route::middleware('can:manage-users')->group(function () {
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
        });

        Route::middleware('can:manage-shifts')->group(function () {
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

            Route::get('/shift-sales/{shift}', [ShiftSalesController::class, 'show'])
                ->name('admin.shift-sales.show');
        });
    });
});
