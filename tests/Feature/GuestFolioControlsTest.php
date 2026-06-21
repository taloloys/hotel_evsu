<?php

use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        'permission_key' => 'view-folio',
        'description' => 'View folio',
        'module' => 'Accounting',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $this->viewFolioPermission->permission_id,
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

    $this->cleaningChargeCode = ChargeCode::create([
        'charge_code' => 115,
        'description' => 'OTHER CHARGES',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    $this->cashPaymentCode = ChargeCode::create([
        'charge_code' => 403,
        'description' => 'CASH',
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
        'status' => 'OCCUPIED',
        'is_active' => true,
    ]);

    $this->roomB = Room::create([
        'room_number' => '102',
        'room_type' => 'Deluxe',
        'base_rate' => 3000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);
});

test('user can post a charge transaction to a folio', function (): void {
    $guest = Guest::create(['last_name' => 'Cruz', 'first_name' => 'Juan']);
    $folio = Folio::create(['folio_number' => 'REG-2026001', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transaction', $folio->folio_id), [
            'charge_code' => $this->cleaningChargeCode->charge_code,
            'type' => 'CHARGE',
            'amount' => 350.00,
            'reference_notes' => 'Room cleaning surcharge',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_code' => $this->cleaningChargeCode->charge_code,
        'charge_amount' => 350.00,
        'credit_amount' => 0.00,
        'reference_notes' => 'Room cleaning surcharge',
    ]);
});

test('user can post a payment transaction to a folio', function (): void {
    $guest = Guest::create(['last_name' => 'Cruz', 'first_name' => 'Juan']);
    $folio = Folio::create(['folio_number' => 'REG-2026001', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transaction', $folio->folio_id), [
            'charge_code' => $this->cashPaymentCode->charge_code,
            'type' => 'PAYMENT',
            'amount' => 500.00,
            'reference_notes' => 'Cash downpayment',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_code' => $this->cashPaymentCode->charge_code,
        'charge_amount' => 0.00,
        'credit_amount' => 500.00,
        'reference_notes' => 'Cash downpayment',
    ]);
});

test('user can transfer guest to another room and update rate', function (): void {
    $guest = Guest::create(['last_name' => 'Cruz', 'first_name' => 'Juan']);
    $folio = Folio::create(['folio_number' => 'REG-2026001', 'guest_id' => $guest->guest_id, 'status' => 'OPEN', 'net_rate' => 2000.00]);
    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => now()->toDateString(),
        'arrival_time' => '14:00',
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '12:00',
        'actual_check_in' => now(),
        'status' => 'CHECKED_IN',
    ]);

    // 1. Transfer to Room B without specifying custom rate (should default to Room B's base_rate which is 3000.00)
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transfer', $booking->booking_id), [
            'new_room_id' => $this->roomB->room_id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'booking_id' => $booking->booking_id,
        'room_id' => $this->roomB->room_id,
    ]);

    $this->assertEquals(3000.00, $folio->refresh()->net_rate);

    // Make room B available again to test custom rate transfer
    $this->roomB->update(['status' => 'AVAILABLE']);
    $this->roomA->update(['status' => 'AVAILABLE']);

    // 2. Transfer back to Room A with a custom higher rate (e.g. 2500.00)
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.transfer', $booking->booking_id), [
            'new_room_id' => $this->roomA->room_id,
            'net_rate' => 2500.00,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertEquals(2500.00, $folio->refresh()->net_rate);
});

test('user can check out a guest via the folio check-out redirect endpoint', function (): void {
    $guest = Guest::create(['last_name' => 'Cruz', 'first_name' => 'Juan']);
    $folio = Folio::create(['folio_number' => 'REG-2026001', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);
    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => now()->toDateString(),
        'arrival_time' => '14:00',
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '12:00',
        'actual_check_in' => now(),
        'status' => 'CHECKED_IN',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.checkout', $booking->booking_id), [
            'checkout_time' => '11:45',
            'checkout_period' => 'AM',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'booking_id' => $booking->booking_id,
        'status' => 'CHECKED_OUT',
    ]);

    $this->assertDatabaseHas('rooms', [
        'room_id' => $this->roomA->room_id,
        'status' => 'CLEANING',
    ]);
});

test('user can close and reopen folio', function (): void {
    $guest = Guest::create(['last_name' => 'Cruz', 'first_name' => 'Juan']);
    $folio = Folio::create(['folio_number' => 'REG-2026001', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    // Close
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.close', $folio->folio_id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertEquals('CLOSED', $folio->refresh()->status);

    // Reopen
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.reopen', $folio->folio_id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertEquals('OPEN', $folio->refresh()->status);
});

test('user can check in a reserved guest via the folio check-in endpoint', function (): void {
    $guest = Guest::create(['last_name' => 'Santos', 'first_name' => 'Maria']);
    $folio = Folio::create(['folio_number' => 'REG-2026002', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);
    $booking = Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $this->roomB->room_id, // roomB is AVAILABLE
        'arrival_date' => now()->toDateString(),
        'arrival_time' => '14:00',
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '12:00',
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.guest-folio.checkin', $booking->booking_id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'booking_id' => $booking->booking_id,
        'status' => 'CHECKED_IN',
    ]);

    $this->assertDatabaseHas('rooms', [
        'room_id' => $this->roomB->room_id,
        'status' => 'OCCUPIED',
    ]);
});
