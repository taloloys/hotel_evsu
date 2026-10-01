<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use App\Services\FacilityBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
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

test('public can view facility set show page with included facilities', function () {
    $response = $this->get(route('facilities.sets.show', $this->functionHallSet));

    $response->assertStatus(200);
    $response->assertSee('Function Hall');
    $response->assertSee('Consolidated Facility Set');
    $response->assertSee('This reservation includes:');
    $response->assertSee('Function Hall A');
    $response->assertSee('Function Hall B');
    $response->assertSee('300 Pax');
});

test('public can view facility set booking form and sees member facilities notice', function () {
    $response = $this->get(route('facilities.sets.book', $this->functionHallSet));

    $response->assertStatus(200);
    $response->assertSee('Function Hall');
    $response->assertSee('This reservation includes:');
    $response->assertSee('Function Hall A');
    $response->assertSee('Function Hall B');
});

test('public can successfully submit consolidated facility set booking and receive dynamic reference number', function () {
    $date = now()->addDays(4)->toDateString();

    $response = $this->post(route('facilities.sets.submit', $this->functionHallSet), [
        'booker_name' => 'Dr. Maria Santos',
        'booker_email' => 'maria.santos@example.com',
        'booker_contact' => '09123456789',
        'event_name' => 'Regional Academic Symposium',
        'event_details' => 'University conference and breakout discussions.',
        'billing_type' => 'daily',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '08:00',
        'end_time' => '17:00',
        'terms_accepted' => '1',
    ]);

    $reservation = FacilityReservation::first();
    expect($reservation)->not->toBeNull()
        ->and($reservation->isConsolidated())->toBeTrue()
        ->and($reservation->reference_number)->toContain('#EVSUOCFH-')
        ->and($reservation->reservedFacilities->count())->toBe(2);

    $response->assertRedirect(route('facilities.confirmation', $reservation));

    // Follow redirect to confirmation page
    $confirmResponse = $this->get(route('facilities.confirmation', $reservation));
    $confirmResponse->assertStatus(200);
    $confirmResponse->assertSee($reservation->reference_number);
    $confirmResponse->assertSee('Function Hall');
    $confirmResponse->assertSee('Consolidated Set');
    $confirmResponse->assertSee('Function Hall A');
    $confirmResponse->assertSee('Function Hall B');
});

test('public submitting booking for set with conflicting member facility shows required error message and allows retry', function () {
    $date = now()->addDays(5)->toDateString();

    // Facility A has already been reserved individually
    app(FacilityBookingService::class)->createReservation([
        'booker_name' => 'Existing Reservee',
        'booker_email' => 'existing@example.com',
        'booker_contact' => '09111111111',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'estimated_amount' => 3200.00,
        'status' => 'approved',
    ], $this->hallA);

    // Attempt to book the entire Function Hall set overlapping that slot
    $response = $this->from(route('facilities.sets.book', $this->functionHallSet))
        ->post(route('facilities.sets.submit', $this->functionHallSet), [
            'booker_name' => 'New Booker',
            'booker_email' => 'new@example.com',
            'booker_contact' => '09222222222',
            'billing_type' => 'hourly',
            'reservation_date' => $date,
            'end_date' => $date,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'terms_accepted' => '1',
        ]);

    $response->assertRedirect(route('facilities.sets.book', $this->functionHallSet));
    $response->assertSessionHasErrors([
        'reservation_date' => 'This facility set is unavailable for the selected date and time because one or more included facilities have already been reserved. Please choose another date or time.',
    ]);

    // Ensure only 1 reservation exists in DB (no partial booking of Hall B)
    expect(FacilityReservation::count())->toBe(1);
});
