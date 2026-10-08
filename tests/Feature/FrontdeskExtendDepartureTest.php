<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // 1. Setup Frontdesk Role & Permission
    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $this->manageReservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $this->manageReservationsPermission->permission_id,
    ]);

    // 2. Frontdesk User
    $this->frontdeskUser = User::factory()->create([
        'username' => 'frontdesk_agent',
        'full_name' => 'Front Desk Agent',
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    // 3. Charge code with slug for Room Charges
    ChargeCode::create([
        'charge_code' => 100,
        'slug' => 'room_charge',
        'description' => 'ROOM CHARGE',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    // 4. Active Shift
    Shift::create([
        'user_id' => $this->frontdeskUser->user_id,
        'start_time' => now(),
    ]);

    // 5. Room
    $this->room = Room::create([
        'room_number' => '201',
        'room_type' => 'Deluxe Room',
        'base_rate' => 2500.00,
        'status' => 'RESERVED',
        'is_active' => true,
    ]);

    // 6. Guest
    $this->guest = Guest::create([
        'first_name' => 'Palma',
        'last_name' => 'Adams',
        'contact_number' => '09123456789',
    ]);

    // 7. Folio
    $this->folio = Folio::create([
        'folio_number' => 'RSV-20260831-0001',
        'guest_id' => $this->guest->guest_id,
        'market_segment' => 'Walk-in',
        'net_rate' => 2500.00,
        'status' => 'OPEN',
    ]);
});

test('staff can extend departure for a reserved booking (late check-in client calling frontdesk)', function (): void {
    Carbon::setTestNow('2026-08-31 10:00:00');

    $booking = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-08-31',
        'arrival_time' => '12:00',
        'departure_date' => '2026-09-01',
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->postJson(route('frontdesk.booking.extend'), [
            'booking_id' => $booking->booking_id,
            'departure_date' => '2026-09-03',
            'departure_time' => '14:00',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $booking->refresh();
    expect($booking->departure_date->toDateString())->toBe('2026-09-03')
        ->and($booking->departure_time)->toBe('14:00')
        ->and($booking->status)->toBe('RESERVED');
});

test('staff can extend departure for a checked-in booking', function (): void {
    Carbon::setTestNow('2026-08-31 10:00:00');

    $this->room->update(['status' => 'OCCUPIED']);

    $booking = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-08-31',
        'arrival_time' => '12:00',
        'departure_date' => '2026-09-01',
        'departure_time' => '12:00',
        'actual_check_in' => '2026-08-31 10:30:00',
        'status' => 'CHECKED_IN',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->postJson(route('frontdesk.booking.extend'), [
            'booking_id' => $booking->booking_id,
            'departure_date' => '2026-09-04',
            'departure_time' => '12:00',
            'net_rate' => 2600.00,
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $booking->refresh();
    expect($booking->departure_date->toDateString())->toBe('2026-09-04')
        ->and((float) $booking->folio->net_rate)->toBe(2600.00);
});

test('rejects extension when new departure date is on or before current departure date', function (): void {
    Carbon::setTestNow('2026-08-31 10:00:00');

    $booking = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-08-31',
        'arrival_time' => '12:00',
        'departure_date' => '2026-09-02',
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->postJson(route('frontdesk.booking.extend'), [
            'booking_id' => $booking->booking_id,
            'departure_date' => '2026-09-01',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

test('rejects extension when room has a conflict with another booking during extended period', function (): void {
    Carbon::setTestNow('2026-08-31 10:00:00');

    $booking1 = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-08-31',
        'arrival_time' => '12:00',
        'departure_date' => '2026-09-02',
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    // Another guest booked the same room starting 2026-09-03
    $guest2 = Guest::create(['first_name' => 'Jane', 'last_name' => 'Smith']);
    $folio2 = Folio::create(['folio_number' => 'RSV-0002', 'guest_id' => $guest2->guest_id]);
    Booking::create([
        'folio_id' => $folio2->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-09-03',
        'arrival_time' => '12:00',
        'departure_date' => '2026-09-05',
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    // Attempt to extend booking1 to 2026-09-04 (overlaps with booking2)
    $response = $this->actingAs($this->frontdeskUser)
        ->postJson(route('frontdesk.booking.extend'), [
            'booking_id' => $booking1->booking_id,
            'departure_date' => '2026-09-04',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    expect($response->json('message'))->toContain('Room 201 is not available for the requested extension period');
});

test('staff can move reservation dates and it records an activity log entry', function (): void {
    Carbon::setTestNow('2026-08-31 10:00:00');

    $booking = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room->room_id,
        'arrival_date' => '2026-08-31',
        'arrival_time' => '14:00',
        'departure_date' => '2026-09-02',
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->postJson(route('frontdesk.booking.move-date'), [
            'booking_id' => $booking->booking_id,
            'arrival_date' => '2026-09-05',
            'arrival_time' => '15:00',
            'departure_date' => '2026-09-08',
            'departure_time' => '11:00',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $booking->refresh();
    expect($booking->arrival_date->toDateString())->toBe('2026-09-05')
        ->and($booking->arrival_time)->toBe('15:00')
        ->and($booking->departure_date->toDateString())->toBe('2026-09-08')
        ->and($booking->departure_time)->toBe('11:00');

    $this->assertDatabaseHas('activitylogs', [
        'action_type' => 'RESERVATION_MOVED',
    ]);

    $log = ActivityLog::where('action_type', 'RESERVATION_MOVED')->latest('log_id')->first();
    expect($log)->not->toBeNull();
    expect($log->description)->toContain("Moved reservation dates for {$this->guest->first_name} {$this->guest->last_name}");
    expect($log->description)->toContain('09/05/2026');
    expect($log->description)->toContain('09/08/2026');
});
