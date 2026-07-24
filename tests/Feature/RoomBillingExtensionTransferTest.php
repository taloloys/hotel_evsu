<?php

use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use App\Services\RoomChargeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // 1. Setup Roles
    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONTDESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    // 2. Setup Permissions
    $this->viewFolioPermission = Permission::create([
        'permission_key' => 'view-guest-folio',
        'description' => 'View guest folio',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->manageFolioPermission = Permission::create([
        'permission_key' => 'manage-guest-folio',
        'description' => 'Manage guest folio',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->manageReservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $this->viewFolioPermission->permission_id,
        $this->manageFolioPermission->permission_id,
        $this->manageReservationsPermission->permission_id,
    ]);

    // 3. Create User
    $this->frontdeskUser = User::factory()->create([
        'username' => 'frontdesk_test',
        'full_name' => 'Front Desk Agent',
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    // 4. Create dummy ChargeCodes
    $this->roomChargeCode = ChargeCode::create([
        'charge_code' => 100,
        'description' => 'ROOM CHARGE',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    ChargeCode::create([
        'charge_code' => 403,
        'description' => 'CASH PAYMENT',
        'category' => 'PAYMENT',
        'is_active' => true,
    ]);

    // 5. Setup basic shift
    $this->shift = Shift::create([
        'user_id' => $this->frontdeskUser->user_id,
        'start_time' => now(),
    ]);

    // Create Rooms
    $this->roomA = Room::create([
        'room_number' => '101',
        'room_type' => 'Standard',
        'base_rate' => 2000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $this->roomB = Room::create([
        'room_number' => '102',
        'room_type' => 'Deluxe',
        'base_rate' => 3000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $this->guest = Guest::create([
        'first_name' => 'Juan',
        'last_name' => 'Cruz',
        'contact_number' => '09171234567',
    ]);
});

test('walk-in check-in posts night 1 charge per-night daily', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    // 1. Without rate override (uses room base rate 2000.00)
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.checkin.store'), [
            'guest_id' => $this->guest->guest_id,
            'room_id' => $this->roomA->room_id,
            'arrival_date' => '2026-07-11',
            'arrival_time' => '12:00',
            'departure_date' => '2026-07-13', // 2 nights
            'departure_time' => '12:00',
            'payment_method' => 'Cash',
        ]);

    $response->assertRedirect();

    $folio = Folio::latest('folio_id')->first();
    $this->assertEquals(2000.00, $folio->net_rate);

    // Verify 1 night of room charge is posted initially (per-night daily)
    $this->assertDatabaseCount('transactions', 1);
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_amount' => 2000.00,
        'transaction_date' => '2026-07-11 00:00:00',
    ]);

    // 2. With rate override (uses custom rate 1800.00)
    $this->roomB->update(['status' => 'AVAILABLE']);
    $responseOverride = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.checkin.store'), [
            'guest_id' => $this->guest->guest_id,
            'room_id' => $this->roomB->room_id,
            'arrival_date' => '2026-07-11',
            'arrival_time' => '12:00',
            'departure_date' => '2026-07-13', // 2 nights
            'departure_time' => '12:00',
            'payment_method' => 'Cash',
            'net_rate' => 1800.00,
        ]);

    $responseOverride->assertRedirect();

    $folioOverride = Folio::latest('folio_id')->first();
    $this->assertEquals(1800.00, $folioOverride->net_rate);

    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folioOverride->folio_id,
        'charge_amount' => 1800.00,
        'transaction_date' => '2026-07-11 00:00:00',
    ]);
});

test('open stay check-in and daily room charge command', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.checkin.store'), [
            'guest_id' => $this->guest->guest_id,
            'room_id' => $this->roomA->room_id,
            'arrival_date' => '2026-07-11',
            'arrival_time' => '12:00',
            'open_stay' => true,
            'payment_method' => 'Cash',
        ]);

    $response->assertRedirect();

    $booking = Booking::latest('booking_id')->first();
    $folio = $booking->folio;
    $this->assertNull($booking->departure_date);

    // Night 1 is posted on arrival date check-in
    $this->assertDatabaseCount('transactions', 1);

    // Mock time to next day: 2026-07-12
    Carbon::setTestNow('2026-07-12 00:05:00');

    // Run the daily post command
    Artisan::call('app:post-nightly-room-charges');

    // Verify Night 2 room charge is posted for 2026-07-12
    $this->assertDatabaseCount('transactions', 2);
    $this->assertTrue(Transaction::where('folio_id', $folio->folio_id)
        ->where('charge_amount', 2000.00)
        ->whereDate('transaction_date', '2026-07-12')
        ->exists());
});

test('stay extension with rate override and billing', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $folio = Folio::create([
        'folio_number' => 'REG-001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
        'net_rate' => 2000.00,
    ]);

    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => '2026-07-11',
        'arrival_time' => '12:00',
        'departure_date' => '2026-07-12', // 1 night
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);

    // Post initial charges (Night 1)
    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 1);

    // Extend stay to 2026-07-14 (total 3 nights) and override rate to 1900.00
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.extend', $booking->booking_id), [
            'departure_date' => '2026-07-14',
            'departure_time' => '12:00',
            'net_rate' => 1900.00,
        ]);

    $response->assertRedirect();

    $booking->refresh();
    $folio->refresh();

    $this->assertEquals('2026-07-14', $booking->departure_date->toDateString());
    $this->assertEquals(1900.00, $folio->net_rate);

    // Night 1 updated to 1900.00 (today is July 11)
    $this->assertDatabaseCount('transactions', 1);
    $this->assertEquals(1900.00, Transaction::where('folio_id', $folio->folio_id)->first()->charge_amount);

    // Advance to July 12 and July 13 to verify subsequent nights post per-night daily
    Carbon::setTestNow('2026-07-12 01:00:00');
    app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
    $this->assertDatabaseCount('transactions', 2);

    Carbon::setTestNow('2026-07-13 01:00:00');
    app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
    $this->assertDatabaseCount('transactions', 3);
});

test('room transfer same-day', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $folio = Folio::create([
        'folio_number' => 'REG-001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
        'net_rate' => 2000.00,
    ]);

    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => '2026-07-11',
        'arrival_time' => '12:00',
        'departure_date' => '2026-07-13', // 2 nights
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);

    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 1);

    // Perform same-day transfer to Room B
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transfer', $booking->booking_id), [
            'new_room_id' => $this->roomB->room_id,
            'net_rate' => 2800.00,
        ]);

    $response->assertRedirect();

    $booking->refresh();
    $folio->refresh();

    // Verify same booking room is updated, and rate is updated to 2800
    $this->assertEquals($this->roomB->room_id, $booking->room_id);
    $this->assertEquals(2800.00, $folio->net_rate);

    // Check that charge was updated to 2800.00
    $transactions = Transaction::where('folio_id', $folio->folio_id)->get();
    $this->assertCount(1, $transactions);
    $this->assertEquals(2800.00, $transactions->first()->charge_amount);
});

test('room transfer multi-day', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $folio = Folio::create([
        'folio_number' => 'REG-001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
        'net_rate' => 2000.00,
    ]);

    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => '2026-07-11',
        'arrival_time' => '12:00',
        'departure_date' => '2026-07-14', // 3 nights (11, 12, 13)
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);

    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 1);

    // Travel to day 2 (July 12)
    Carbon::setTestNow('2026-07-12 14:00:00');
    app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
    $this->assertDatabaseCount('transactions', 2);

    // Transfer guest to Room B (net rate 2700.00)
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transfer', $booking->booking_id), [
            'new_room_id' => $this->roomB->room_id,
            'net_rate' => 2700.00,
        ]);

    $response->assertRedirect();

    $booking->refresh();
    $folio->refresh();

    // Verify old booking check-out is set to today
    $this->assertEquals('2026-07-12', $booking->departure_date->toDateString());

    // Verify new booking is created for Room B starting TOMORROW (July 13) ending July 14
    $newBooking = Booking::where('folio_id', $folio->folio_id)
        ->where('booking_id', '!=', $booking->booking_id)
        ->first();

    $this->assertNotNull($newBooking);
    $this->assertEquals($this->roomB->room_id, $newBooking->room_id);
    $this->assertEquals('2026-07-13', $newBooking->arrival_date->toDateString());
    $this->assertEquals('2026-07-14', $newBooking->departure_date->toDateString());

    // Travel to day 3 (July 13) for new booking charge to post
    Carbon::setTestNow('2026-07-13 10:00:00');
    app(RoomChargeService::class)->processCatchUpCharges($newBooking->booking_id);

    $transactions = Transaction::where('folio_id', $folio->folio_id)->get();

    // There should be 3 total charges: 2 from old booking (Jul 11, 12 @ 2000), 1 from new booking (Jul 13 @ 2700)
    $this->assertCount(3, $transactions);

    $oldCharges = Transaction::where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')->get();
    $this->assertCount(2, $oldCharges);
    foreach ($oldCharges as $txn) {
        $this->assertEquals(2000.00, $txn->charge_amount);
    }

    $newCharges = Transaction::where('charge_number', 'like', 'RM-'.$newBooking->booking_id.'-%')->get();
    $this->assertCount(1, $newCharges);
    $this->assertEquals(2700.00, $newCharges->first()->charge_amount);
});

test('early check-out on day 2 of 3-night stay only charges stayed nights', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $folio = Folio::create([
        'folio_number' => 'REG-999',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
        'net_rate' => 2000.00,
    ]);

    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => '2026-07-11',
        'arrival_time' => '12:00',
        'departure_date' => '2026-07-14', // Scheduled 3 nights
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);

    // Day 1: Night 1 posted (₱2,000)
    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 1);
    $this->assertEquals(2000.00, $folio->refresh()->balance);

    // Day 2 (July 12): Night 2 posted (total ₱4,000 balance)
    Carbon::setTestNow('2026-07-12 09:00:00');
    app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
    $this->assertDatabaseCount('transactions', 2);
    $this->assertEquals(4000.00, $folio->refresh()->balance);

    // Guest pays the 2 nights stayed (₱4,000)
    Transaction::create([
        'folio_id' => $folio->folio_id,
        'charge_code' => 403, // CASH PAYMENT
        'shift_id' => $this->shift->shift_id,
        'user_id' => $this->frontdeskUser->user_id,
        'transaction_date' => '2026-07-12',
        'charge_number' => 'PAY-001',
        'payment_method' => 'CASH',
        'reference_notes' => 'Early checkout payment',
        'charge_amount' => 0.00,
        'credit_amount' => 4000.00,
    ]);

    $this->assertEquals(0.00, $folio->refresh()->balance);

    // Perform Early Checkout on July 12
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.checkout', $booking->booking_id), [
            'checkout_time' => '10:00',
            'checkout_period' => 'AM',
        ]);

    $response->assertRedirect();

    $booking->refresh();
    $folio->refresh();

    $this->assertEquals('CHECKED_OUT', $booking->status);
    $this->assertEquals('2026-07-12', $booking->departure_date->toDateString());
    $this->assertEquals('CLOSED', $folio->status);

    // Night 3 (July 13) was NEVER charged, total charges count is 2 (plus 1 payment = 3 transactions total)
    $this->assertDatabaseCount('transactions', 3);
});
