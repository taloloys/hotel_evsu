<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\FacilityBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $resPerm = Permission::firstOrCreate(
        ['permission_key' => 'manage-reservations'],
        ['description' => 'Manage reservations', 'module' => 'Front Desk', 'is_active' => true]
    );

    $this->role = Role::firstOrCreate(
        ['role_name' => 'FRONT_DESK'],
        ['description' => 'Front Desk Staff', 'is_active' => true]
    );

    $this->role->permissions()->syncWithoutDetaching([$resPerm->permission_id]);

    $this->staff = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);

    $this->hallA = Facility::create([
        'name' => 'Function Hall A',
        'capacity' => 150,
        'rate' => 8000.00,
        'rate_type' => 'daily',
        'hourly_rate' => 800.00,
        'daily_rate' => 8000.00,
        'is_active' => true,
    ]);

    $this->hallB = Facility::create([
        'name' => 'Function Hall B',
        'capacity' => 150,
        'rate' => 8000.00,
        'rate_type' => 'daily',
        'hourly_rate' => 800.00,
        'daily_rate' => 8000.00,
        'is_active' => true,
    ]);

    $this->functionHallSet = FacilitySet::create([
        'name' => 'Function Hall',
        'prefix_code' => 'EVSUOCFH',
        'description' => 'Combined function hall set.',
        'capacity' => 300,
        'hourly_rate' => 1500.00,
        'daily_rate' => 15000.00,
        'rate' => 15000.00,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    $this->functionHallSet->facilities()->attach([
        $this->hallA->facility_id,
        $this->hallB->facility_id,
    ]);
});

test('frontdesk staff can view create reservation page with facility sets listed', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('frontdesk.facility-reservations.create'));

    $response->assertStatus(200);
    $response->assertSee('Consolidated Facility Sets');
    $response->assertSee('[SET] Function Hall');
    $response->assertSee('Function Hall A');
    $response->assertSee('Function Hall B');
});

test('frontdesk staff can book a consolidated facility set reservation with dynamic reference', function () {
    $date = now()->addDays(5)->toDateString();

    $response = $this->actingAs($this->staff)
        ->post(route('frontdesk.facility-reservations.store'), [
            'facility_id' => 'set_'.$this->functionHallSet->facility_set_id,
            'booker_name' => 'Dean Dela Cruz',
            'booker_email' => 'dean@evsu.edu.ph',
            'booker_contact' => '09123456789',
            'event_name' => 'College Convocation',
            'event_details' => 'Full venue required.',
            'billing_type' => 'daily',
            'reservation_date' => $date,
            'end_date' => $date,
            'start_time' => '08:00',
            'end_time' => '17:00',
            'status' => 'approved',
        ]);

    $reservation = FacilityReservation::first();
    expect($reservation)->not->toBeNull()
        ->and($reservation->isConsolidated())->toBeTrue()
        ->and($reservation->facility_set_id)->toBe($this->functionHallSet->facility_set_id)
        ->and($reservation->reference_number)->toContain('#EVSUOCFH-');

    $response->assertRedirect(route('frontdesk.facility-reservations.show', $reservation));

    // View show page
    $showResponse = $this->actingAs($this->staff)
        ->get(route('frontdesk.facility-reservations.show', $reservation));

    $showResponse->assertStatus(200);
    $showResponse->assertSee($reservation->reference_number);
    $showResponse->assertSee('Consolidated Set');
    $showResponse->assertSee('Function Hall');
    $showResponse->assertSee('Function Hall A');
    $showResponse->assertSee('Function Hall B');
});

test('frontdesk staff can approve a pending facility set reservation and checks conflicts atomically', function () {
    $date = now()->addDays(6)->toDateString();

    $reservation = app(FacilityBookingService::class)->createReservation([
        'booker_name' => 'Pending Client',
        'booker_email' => 'pending@example.com',
        'booker_contact' => '09998887777',
        'billing_type' => 'daily',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 15000.00,
        'status' => 'pending',
    ], $this->functionHallSet);

    expect($reservation->status)->toBe('pending');

    $response = $this->actingAs($this->staff)
        ->patch(route('frontdesk.facility-reservations.approve', $reservation));

    $response->assertRedirect(route('frontdesk.facility-reservations.index'));
    expect($reservation->fresh()->status)->toBe('approved');
});

test('frontdesk cannot approve a pending set reservation if a member facility has conflicting reservation', function () {
    $date = now()->addDays(7)->toDateString();

    $setReservation = app(FacilityBookingService::class)->createReservation([
        'booker_name' => 'Set Booker',
        'booker_email' => 'setbooker@example.com',
        'booker_contact' => '09998887776',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '10:00:00',
        'end_time' => '15:00:00',
        'estimated_amount' => 7500.00,
        'status' => 'pending',
    ], $this->functionHallSet);

    // Later, Hall A was approved for overlapping time
    app(FacilityBookingService::class)->createReservation([
        'booker_name' => 'Competing Booker',
        'booker_email' => 'competing@example.com',
        'booker_contact' => '09998887775',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '11:00:00',
        'end_time' => '14:00:00',
        'estimated_amount' => 2400.00,
        'status' => 'approved',
    ], $this->hallA);

    $response = $this->actingAs($this->staff)
        ->patch(route('frontdesk.facility-reservations.approve', $setReservation));

    $response->assertSessionHas('error');
    expect($setReservation->fresh()->status)->toBe('pending');
});
