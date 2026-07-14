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

test('walk-in check-in with rate override vs. default rate', function () {
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

    // Verify 2 nights of room charges are posted immediately at 2000.00 each
    $this->assertDatabaseCount('transactions', 2);
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_amount' => 2000.00,
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

    // For open stay, no room charges should be posted initially
    $this->assertDatabaseCount('transactions', 0);

    // Mock time to next day: 2026-07-12 midnight/morning (yesterday was 2026-07-11)
    Carbon::setTestNow('2026-07-12 00:05:00');

    // Run the daily post command
    Artisan::call('billing:post-daily-charges');

    // Verify room charge is posted for night of 2026-07-11
    $this->assertDatabaseCount('transactions', 1);
    $this->assertTrue(Transaction::where('folio_id', $folio->folio_id)
        ->where('charge_amount', 2000.00)
        ->whereDate('transaction_date', '2026-07-11')
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

    // Post initial charges (1 night)
    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 1);

    // Extend stay to 2026-07-14 (total 3 nights, 2 new nights) and override rate to 1900.00
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

    // Verify 3 room charges exist (1 updated original night, 2 new nights) all with rate 1900.00
    $this->assertDatabaseCount('transactions', 3);
    $transactions = Transaction::where('folio_id', $folio->folio_id)->get();
    foreach ($transactions as $txn) {
        $this->assertEquals(1900.00, $txn->charge_amount);
    }
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
    $this->assertDatabaseCount('transactions', 2);

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

    // Check that charges were updated to 2800.00
    $transactions = Transaction::where('folio_id', $folio->folio_id)->get();
    $this->assertCount(2, $transactions);
    foreach ($transactions as $txn) {
        $this->assertEquals(2800.00, $txn->charge_amount);
    }
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
    $this->assertDatabaseCount('transactions', 3);

    // Travel to day 2 (July 12)
    Carbon::setTestNow('2026-07-12 14:00:00');

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

    // Verify total charges:
    // Old booking: Nights of July 11, 12 preserved (2000.00 each). Only July 13 deleted.
    // New booking: Night of July 13 (2700.00)
    $transactions = Transaction::where('folio_id', $folio->folio_id)->get();

    // There should be 3 total charges: 2 from old booking, 1 from new booking
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

test('room transfer preserves today charges and prevents double billing', function () {
    Carbon::setTestNow('2026-07-11 12:00:00');

    $folio = Folio::create([
        'folio_number' => 'REG-002',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
        'net_rate' => 2500.00,
    ]);

    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => '2026-07-11',
        'arrival_time' => '14:00',
        'departure_date' => '2026-07-15', // 4 nights (11, 12, 13, 14)
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);

    $booking->postRoomCharges();
    $this->assertDatabaseCount('transactions', 4);

    // Travel to day 3 (July 13, mid-stay)
    Carbon::setTestNow('2026-07-13 10:00:00');

    // Transfer guest to Room B
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transfer', $booking->booking_id), [
            'new_room_id' => $this->roomB->room_id,
            'net_rate' => 3000.00,
        ]);

    $response->assertRedirect();

    $booking->refresh();
    $folio->refresh();

    // 1. Today's room charges are preserved after transfer
    $todayOldCharges = Transaction::where('folio_id', $folio->folio_id)
        ->where('charge_code', 100)
        ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
        ->whereDate('transaction_date', '<=', '2026-07-13')
        ->get();
    $this->assertCount(3, $todayOldCharges, 'Old booking charges for July 11, 12, 13 must be preserved');

    // 2. The new room booking starts tomorrow
    $newBooking = Booking::where('folio_id', $folio->folio_id)
        ->where('booking_id', '!=', $booking->booking_id)
        ->first();

    $this->assertNotNull($newBooking);
    $this->assertEquals('2026-07-14', $newBooking->arrival_date->toDateString(), 'New booking must start tomorrow');
    $this->assertEquals('2026-07-15', $newBooking->departure_date->toDateString());
    $this->assertEquals('CHECKED_IN', $newBooking->status);

    // 3. No orphan or double billing entries exist for the transition day
    $allCharges = Transaction::where('folio_id', $folio->folio_id)
        ->where('charge_code', 100)
        ->get();

    // 3 old (Jul 11, 12, 13) + 1 new (Jul 14) = 4 total
    $this->assertCount(4, $allCharges, 'Total charges must equal nights of stay with no duplicates');

    // Verify no duplicate charges for today (July 13)
    $todayAllCharges = $allCharges->filter(function ($txn) {
        return Carbon::parse($txn->transaction_date)->toDateString() === '2026-07-13';
    });
    $this->assertCount(1, $todayAllCharges, 'There must be exactly one charge for the transition day');

    // Verify old charges at old rate, new charges at new rate
    foreach ($todayOldCharges as $txn) {
        $this->assertEquals(2500.00, $txn->charge_amount);
    }
    $newCharges = Transaction::where('charge_number', 'like', 'RM-'.$newBooking->booking_id.'-%')->get();
    $this->assertCount(1, $newCharges);
    $this->assertEquals(3000.00, $newCharges->first()->charge_amount);
});
