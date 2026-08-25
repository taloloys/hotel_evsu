<?php

use App\Mail\CheckInConfirmationMail;
use App\Mail\PaymentReceiptMail;
use App\Mail\ReservationConfirmationMail;
use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $role = Role::firstOrCreate(
        ['role_name' => 'SUPER_ADMIN'],
        ['description' => 'Super Administrator', 'is_system_admin' => true, 'is_active' => true]
    );

    $this->user = User::factory()->create([
        'role_id' => $role->role_id,
        'email' => 'admin@evsu.edu.ph',
    ]);

    $this->guest = Guest::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'klenth240@gmail.com',
    ]);

    $this->room = Room::create([
        'room_number' => '101',
        'room_type' => 'Standard',
        'base_rate' => 1500,
        'status' => 'AVAILABLE',
    ]);
});

test('reservation creation queues ReservationConfirmationMail', function () {
    $this->withoutExceptionHandling();

    $response = $this->actingAs($this->user)
        ->post(route('frontdesk.reservation.store'), [
            'first_name' => $this->guest->first_name,
            'last_name' => $this->guest->last_name,
            'email' => $this->guest->email,
            'folio_number' => 'REG-12345',
            'registration_number' => 'REGNUM-12345',
            'room_id' => $this->room->room_id,
            'arrival_date' => now()->toDateString(),
            'arrival_time' => '14:00',
            'departure_date' => now()->addDays(3)->toDateString(),
            'departure_time' => '12:00',
            'num_pax' => 2,
        ]);

    $response->assertRedirect();

    Mail::assertQueued(ReservationConfirmationMail::class);
});

test('guest checkin queues CheckInConfirmationMail', function () {
    $this->withoutExceptionHandling();
    $folio = Folio::create([
        'folio_number' => 'FOL-001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
    ]);

    $booking = Booking::create([
        'guest_id' => $this->guest->guest_id,
        'room_id' => $this->room->room_id,
        'folio_id' => $folio->folio_id,
        'arrival_date' => now()->format('Y-m-d'),
        'departure_date' => now()->addDays(3)->format('Y-m-d'),
        'num_pax' => 2,
        'net_rate' => 1500,
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('frontdesk.guest-folio.checkin', $booking->booking_id));

    $response->assertRedirect();

    Mail::assertQueued(CheckInConfirmationMail::class);
});

test('adding payment transaction queues PaymentReceiptMail', function () {
    $this->withoutExceptionHandling();

    $folio = Folio::create([
        'folio_number' => 'FOL-002',
        'guest_id' => $this->guest->guest_id,
        'status' => 'OPEN',
    ]);

    $chargeCode = ChargeCode::firstOrCreate([
        'charge_code' => '403',
    ], [
        'description' => 'CASH',
        'is_active' => true,
        'category' => 'PAYMENT',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('frontdesk.guest-folio.transaction', $folio->folio_id), [
            'charge_code' => $chargeCode->charge_code,
            'type' => 'PAYMENT',
            'amount' => 5000,
            'reference_notes' => 'Room Payment',
        ]);

    $response->assertRedirect();

    Mail::assertQueued(PaymentReceiptMail::class);
});
