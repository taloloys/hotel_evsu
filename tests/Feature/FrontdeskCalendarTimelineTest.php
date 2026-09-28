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

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $this->reservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->staffRole->permissions()->sync([
        $this->reservationsPermission->permission_id,
    ]);

    $this->staffUser = User::factory()->create([
        'username' => 'frontdesk_user',
        'full_name' => 'Front Desk Staff',
        'role_id' => $this->staffRole->role_id,
        'is_active' => true,
    ]);

    // Create sample rooms
    $this->room1 = Room::create([
        'room_number' => '101',
        'room_type' => 'Connecting Room',
        'base_rate' => 1500.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $this->room2 = Room::create([
        'room_number' => '201',
        'room_type' => 'Deluxe Room',
        'base_rate' => 2500.00,
        'status' => 'OCCUPIED',
        'is_active' => true,
    ]);

    $this->guest = Guest::create([
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'contact_number' => '09123456789',
        'email' => 'juan@example.com',
        'guest_type' => 'REGULAR',
    ]);

    $this->folio = Folio::create([
        'folio_number' => 'FOL-1001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'ACTIVE',
        'net_rate' => 2500.00,
    ]);

    $this->booking = Booking::create([
        'folio_id' => $this->folio->folio_id,
        'room_id' => $this->room2->room_id,
        'arrival_date' => Carbon::today()->toDateString(),
        'departure_date' => Carbon::today()->addDays(3)->toDateString(),
        'arrival_time' => '14:00',
        'departure_time' => '12:00',
        'status' => 'CHECKED_IN',
    ]);
});

test('unauthenticated users cannot access calendar timeline data', function (): void {
    $this->getJson(route('frontdesk.calendar.timeline-data'))
        ->assertUnauthorized();
});

test('frontdesk staff can fetch calendar timeline data successfully', function (): void {
    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.calendar.timeline-data'));

    $response->assertOk()
        ->assertJsonStructure([
            'range' => ['start_date', 'end_date', 'days', 'display'],
            'dates',
            'rooms',
            'room_types',
            'summary' => ['total', 'available', 'occupied', 'reserved', 'cleaning', 'maintenance'],
        ]);

    $data = $response->json();
    expect($data['dates'])->toBeArray()->and(count($data['dates']))->toBe(14);
    expect($data['rooms'])->toBeArray()->and(count($data['rooms']))->toBe(2);

    // Verify room 201 contains the active checked-in booking
    $room201 = collect($data['rooms'])->firstWhere('room_number', '201');
    expect($room201)->not->toBeNull();
    expect($room201['bookings'])->toBeArray()->and(count($room201['bookings']))->toBe(1);
    expect($room201['bookings'][0]['guest_name'])->toBe('Juan Dela Cruz');
    expect($room201['bookings'][0]['status'])->toBe('CHECKED_IN');
});

test('calendar timeline data respects custom start_date, days, and room_type filters', function (): void {
    $customStart = Carbon::today()->addDays(5)->toDateString();
    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.calendar.timeline-data', [
            'start_date' => $customStart,
            'days' => 7,
            'room_type' => 'Connecting Room',
        ]));

    $response->assertOk();
    $data = $response->json();

    expect($data['range']['start_date'])->toBe($customStart);
    expect($data['range']['days'])->toBe(7);
    expect(count($data['dates']))->toBe(7);
    expect(count($data['rooms']))->toBe(1);
    expect($data['rooms'][0]['room_type'])->toBe('Connecting Room');
});

test('calendar timeline data reports accurate has_unpaid_balance flag based on folio transactions', function (): void {
    $shift = Shift::create([
        'user_id' => $this->staffUser->user_id,
        'start_time' => Carbon::now()->subHours(2),
    ]);

    $roomChargeCode = ChargeCode::create([
        'charge_code' => 100,
        'slug' => 'room-charge',
        'description' => 'Room Charge',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    $paymentChargeCode = ChargeCode::create([
        'charge_code' => 403,
        'slug' => 'payment-cash',
        'description' => 'Cash Payment',
        'category' => 'PAYMENT',
        'is_active' => true,
    ]);

    // 1. Initially with no transactions, balance is 0.00, has_unpaid_balance is false
    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.calendar.timeline-data'));
    $response->assertOk();
    $data = $response->json();
    $room201 = collect($data['rooms'])->firstWhere('room_number', '201');
    expect($room201['bookings'][0]['has_unpaid_balance'])->toBeFalse();
    expect((float) $room201['bookings'][0]['balance'])->toBe(0.0);

    // 2. Add an unpaid charge transaction
    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'shift_id' => $shift->shift_id,
        'charge_code' => $roomChargeCode->charge_code,
        'user_id' => $this->staffUser->user_id,
        'transaction_date' => Carbon::today(),
        'charge_amount' => 2500.00,
        'credit_amount' => 0.00,
    ]);

    $responseWithCharge = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.calendar.timeline-data'));
    $dataWithCharge = $responseWithCharge->json();
    $room201WithCharge = collect($dataWithCharge['rooms'])->firstWhere('room_number', '201');
    expect($room201WithCharge['bookings'][0]['has_unpaid_balance'])->toBeTrue();
    expect((float) $room201WithCharge['bookings'][0]['balance'])->toBe(2500.0);

    // 3. Fully pay the charge with a credit transaction
    Transaction::create([
        'folio_id' => $this->folio->folio_id,
        'shift_id' => $shift->shift_id,
        'charge_code' => $paymentChargeCode->charge_code,
        'user_id' => $this->staffUser->user_id,
        'transaction_date' => Carbon::today(),
        'charge_amount' => 0.00,
        'credit_amount' => 2500.00,
    ]);

    $responseSettled = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.calendar.timeline-data'));
    $dataSettled = $responseSettled->json();
    $room201Settled = collect($dataSettled['rooms'])->firstWhere('room_number', '201');
    expect($room201Settled['bookings'][0]['has_unpaid_balance'])->toBeFalse();
    expect((float) $room201Settled['bookings'][0]['balance'])->toBe(0.0);
});
