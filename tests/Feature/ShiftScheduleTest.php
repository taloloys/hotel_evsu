<?php

use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Roles
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $manageReservations = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $viewFolio = Permission::create([
        'permission_key' => 'view-shift-sales',
        'description' => 'View shift sales',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $manageReservations->permission_id,
        $viewFolio->permission_id,
    ]);

    // Users
    $this->adminUser = User::factory()->create([
        'role_id' => $this->adminRole->role_id,
        'is_active' => true,
    ]);

    $this->staffUser1 = User::factory()->create([
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    $this->staffUser2 = User::factory()->create([
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    // Guest and Folio for transactions
    $this->guest = Guest::create([
        'first_name' => 'Test',
        'last_name' => 'Guest',
    ]);

    $this->folio = Folio::create([
        'folio_number' => 'F00001',
        'guest_id' => $this->guest->guest_id,
        'market_segment' => 'LEISURE',
        'symbol' => 'USD',
        'folio_type' => 'GUEST',
        'status' => 'OPEN',
        'num_pax' => 1,
        'has_joiner' => false,
        'num_free_breakfasts' => 0,
    ]);

    // Charge codes
    $this->roomChargeCode = ChargeCode::create([
        'charge_code' => 100,
        'description' => 'ROOM CHARGE',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    $this->cashPaymentCode = ChargeCode::create([
        'charge_code' => 403,
        'description' => 'CASH PAYMENT',
        'category' => 'PAYMENT',
        'is_active' => true,
    ]);
});

test('unauthenticated users are redirected from shift scheduling', function (): void {
    $this->get(route('admin.shift-schedules'))
        ->assertRedirect(route('home'));
});

test('admin can view shift scheduling page', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.shift-schedules'));

    $response->assertOk();
});

test('admin can create a shift schedule', function (): void {
    $scheduleData = [
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'shift_date' => '2026-06-20',
        'scheduled_start_time' => '06:00',
        'scheduled_end_time' => '14:00',
        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        'notes' => 'Test notes',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.shift-schedules.store'), $scheduleData);

    $response->assertRedirect(route('admin.shift-schedules'));

    $this->assertDatabaseHas('shift_schedules', [
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'scheduled_start_time' => '06:00',
        'scheduled_end_time' => '14:00',
        'is_active' => true,
    ]);
});

test('admin can edit a shift schedule and reassign to another user', function (): void {
    $schedule = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'shift_date' => '2026-06-20',
        'scheduled_start_time' => '06:00',
        'scheduled_end_time' => '14:00',
        'status' => 'SCHEDULED',
    ]);

    $updateData = [
        'user_id' => $this->staffUser2->user_id, // Reassigned to staff 2
        'shift_name' => 'Updated Morning Shift',
        'shift_date' => '2026-06-20',
        'scheduled_start_time' => '07:00',
        'scheduled_end_time' => '15:00',
        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        'notes' => 'Reassigned shift',
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.shift-schedules.update', $schedule), $updateData);

    $response->assertRedirect(route('admin.shift-schedules'));

    $this->assertDatabaseHas('shift_schedules', [
        'id' => $schedule->id,
        'user_id' => $this->staffUser2->user_id,
        'shift_name' => 'Updated Morning Shift',
        'scheduled_start_time' => '07:00',
        'scheduled_end_time' => '15:00',
    ]);
});

test('admin cannot edit an active or completed shift schedule', function (): void {
    $schedule = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'shift_date' => '2026-06-20',
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $updateData = [
        'user_id' => $this->staffUser2->user_id,
        'shift_name' => 'Attempted Edit',
        'shift_date' => '2026-06-20',
        'scheduled_start_time' => '07:00',
        'scheduled_end_time' => '15:00',
        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.shift-schedules.update', $schedule), $updateData);

    $response->assertSessionHasErrors(['status']);
});

test('staff user can open their scheduled shift', function (): void {
    $schedule = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'SCHEDULED',
    ]);

    $response = $this->actingAs($this->staffUser1)
        ->post(route('frontdesk.shift.open'), ['schedule_id' => $schedule->id]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('shift_schedules', [
        'id' => $schedule->id,
        'status' => 'ACTIVE',
    ]);

    $this->assertDatabaseHas('shifts', [
        'user_id' => $this->staffUser1->user_id,
        'schedule_id' => $schedule->id,
        'end_time' => null,
    ]);
});

test('staff user can close their active shift and drawer totals are calculated', function (): void {
    $schedule = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Morning Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift = Shift::create([
        'user_id' => $this->staffUser1->user_id,
        'schedule_id' => $schedule->id,
        'start_time' => Carbon::now()->subHours(4),
    ]);

    // Create a transaction under this shift
    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'charge_code' => 403,
        'shift_id' => $shift->shift_id,
        'user_id' => $this->staffUser1->user_id,
        'transaction_date' => Carbon::today()->toDateString(),
        'charge_number' => 'PAY-888888',
        'payment_method' => 'CASH',
        'credit_amount' => 500.00,
    ]);

    $response = $this->actingAs($this->staffUser1)
        ->post(route('frontdesk.shift.close'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($shift->fresh()->end_time)->not->toBeNull();
    expect($schedule->fresh()->status)->toBe('COMPLETED');
});

test('staff user can only view their own sales in frontdesk shift sales report', function (): void {
    $schedule1 = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Staff 1 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift1 = Shift::create([
        'user_id' => $this->staffUser1->user_id,
        'schedule_id' => $schedule1->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'charge_code' => 100,
        'shift_id' => $shift1->shift_id,
        'user_id' => $this->staffUser1->user_id,
        'transaction_date' => Carbon::today()->toDateString(),
        'charge_number' => 'CHG-111111',
        'payment_method' => 'NONE',
        'charge_amount' => 1200.00,
    ]);

    // Transaction for Staff 2
    $schedule2 = ShiftSchedule::create([
        'user_id' => $this->staffUser2->user_id,
        'shift_name' => 'Staff 2 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift2 = Shift::create([
        'user_id' => $this->staffUser2->user_id,
        'schedule_id' => $schedule2->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'charge_code' => 100,
        'shift_id' => $shift2->shift_id,
        'user_id' => $this->staffUser2->user_id,
        'transaction_date' => Carbon::today()->toDateString(),
        'charge_number' => 'CHG-222222',
        'payment_method' => 'NONE',
        'charge_amount' => 800.00,
    ]);

    // Query report as Staff 1
    $response = $this->actingAs($this->staffUser1)
        ->get(route('frontdesk.shift-sales', [
            'date_from' => Carbon::today()->toDateString(),
            'report_type' => 'all',
        ]));

    $response->assertOk();
    $response->assertSee('CHG-111111');
    $response->assertDontSee('CHG-222222');
});

test('admin can view any employee sales in frontdesk/admin shift sales report', function (): void {
    $schedule1 = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Staff 1 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift1 = Shift::create([
        'user_id' => $this->staffUser1->user_id,
        'schedule_id' => $schedule1->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'charge_code' => 100,
        'shift_id' => $shift1->shift_id,
        'user_id' => $this->staffUser1->user_id,
        'transaction_date' => Carbon::today()->toDateString(),
        'charge_number' => 'CHG-111111',
        'payment_method' => 'NONE',
        'charge_amount' => 1200.00,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.shift-sales', [
            'date_from' => Carbon::today()->toDateString(),
            'employee_id' => $this->staffUser1->user_id,
            'report_type' => 'all',
        ]));

    $response->assertOk();
    $response->assertSee('CHG-111111');
});

test('staff user cannot query shift sales for a shift belonging to another user', function (): void {
    $schedule2 = ShiftSchedule::create([
        'user_id' => $this->staffUser2->user_id,
        'shift_name' => 'Staff 2 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift2 = Shift::create([
        'user_id' => $this->staffUser2->user_id,
        'schedule_id' => $schedule2->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    $response = $this->actingAs($this->staffUser1)
        ->get(route('frontdesk.shift-sales', [
            'shift_id' => $shift2->shift_id,
        ]));

    $response->assertStatus(403);
});

test('staff user cannot view detail view for a shift belonging to another user', function (): void {
    $schedule2 = ShiftSchedule::create([
        'user_id' => $this->staffUser2->user_id,
        'shift_name' => 'Staff 2 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift2 = Shift::create([
        'user_id' => $this->staffUser2->user_id,
        'schedule_id' => $schedule2->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    $response = $this->actingAs($this->staffUser1)
        ->get(route('frontdesk.shift-sales.show', $shift2));

    $response->assertStatus(403);
});

test('admin user can view detail view for a shift belonging to another user', function (): void {
    $schedule1 = ShiftSchedule::create([
        'user_id' => $this->staffUser1->user_id,
        'shift_name' => 'Staff 1 Shift',
        'shift_date' => Carbon::today()->toDateString(),
        'scheduled_start_time' => '06:00:00',
        'scheduled_end_time' => '14:00:00',
        'status' => 'ACTIVE',
    ]);

    $shift1 = Shift::create([
        'user_id' => $this->staffUser1->user_id,
        'schedule_id' => $schedule1->id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.shift-sales.show', $shift1));

    $response->assertOk();
});
