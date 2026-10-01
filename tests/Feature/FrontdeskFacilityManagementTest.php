<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->facilityPerm = Permission::firstOrCreate(
        ['permission_key' => 'manage-facilities'],
        [
            'description' => 'Manage facilities',
            'module' => 'Front Desk',
            'is_active' => true,
        ]
    );

    $this->reservationPerm = Permission::firstOrCreate(
        ['permission_key' => 'manage-reservations'],
        [
            'description' => 'Manage reservations',
            'module' => 'Front Desk',
            'is_active' => true,
        ]
    );

    $this->role = Role::firstOrCreate(
        ['role_name' => 'FRONT_DESK'],
        ['description' => 'Front Desk Role', 'is_active' => true]
    );

    $this->role->permissions()->syncWithoutDetaching([
        $this->facilityPerm->permission_id,
        $this->reservationPerm->permission_id,
    ]);

    $this->user = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);
});

test('user with manage-facilities can view facilities list', function (): void {
    Facility::create([
        'name' => 'Basketball Gym',
        'rate' => 1000,
        'rate_type' => 'hourly',
        'capacity' => 100,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('frontdesk.facilities.index'));

    $response->assertStatus(200);
    $response->assertSee('Basketball Gym');
    $response->assertSee('Total Facilities');
});

test('user can create a facility from frontdesk', function (): void {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->post(route('frontdesk.facilities.store'), [
        'name' => 'Grand Function Hall',
        'description' => 'Ideal for weddings and banquets.',
        'capacity' => 300,
        'rate' => 15000,
        'rate_type' => 'daily',
        'is_active' => 1,
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $response->assertSessionHas('success');

    $facility = Facility::where('name', 'Grand Function Hall')->first();
    expect($facility)->not->toBeNull();
    expect($facility->rate_type)->toBe('daily');
    expect((float) $facility->rate)->toBe(15000.0);
    expect($facility->capacity)->toBe(300);
});

test('user can edit a facility from frontdesk', function (): void {
    $facility = Facility::create([
        'name' => 'Conference Room A',
        'rate' => 1200,
        'rate_type' => 'hourly',
        'capacity' => 20,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('frontdesk.facilities.update', $facility), [
        'name' => 'Conference Room A (Renovated)',
        'rate' => 1500,
        'rate_type' => 'hourly',
        'capacity' => 25,
        'is_active' => 1,
        'sort_order' => 2,
    ]);

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $facility->refresh();
    expect($facility->name)->toBe('Conference Room A (Renovated)');
    expect((float) $facility->rate)->toBe(1500.0);
});

test('user can toggle active status of a facility', function (): void {
    $facility = Facility::create([
        'name' => 'Outdoor Stage',
        'rate' => 8000,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->patch(route('frontdesk.facilities.toggle', $facility));

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $facility->refresh();
    expect($facility->is_active)->toBeFalse();
});

test('user can delete a facility from frontdesk', function (): void {
    $facility = Facility::create([
        'name' => 'Temporary Tent',
        'rate' => 500,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->delete(route('frontdesk.facilities.destroy', $facility));

    $response->assertRedirect(route('frontdesk.facilities.index'));
    expect(Facility::find($facility->facility_id))->toBeNull();
});

test('frontdesk dashboard displays facilities section and schedule', function (): void {
    $facility = Facility::create([
        'name' => 'Tennis Court',
        'rate' => 300,
        'rate_type' => 'hourly',
        'capacity' => 4,
        'is_active' => true,
    ]);

    FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Maria Santos',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'reservation_date' => now()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 2,
        'estimated_amount' => 600,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('frontdesk.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Hotel Room Monitoring & Calendar', false);
    $response->assertSee('Facilities');
    $response->assertSee('Tennis Court');
    $response->assertSee('Maria Santos');
});

test('front desk can book a facility reservation', function (): void {
    $facility = Facility::create([
        'name' => 'Badminton Court 1',
        'rate' => 200,
        'rate_type' => 'hourly',
        'capacity' => 4,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->post(route('frontdesk.facility-reservations.store'), [
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Carlos Yulo',
        'booker_email' => 'carlos@example.com',
        'booker_contact' => '09171234567',
        'reservation_date' => now()->addDay()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'status' => 'approved',
    ]);

    $reservation = FacilityReservation::where('booker_name', 'Carlos Yulo')->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->status)->toBe('approved');
    expect((float) $reservation->estimated_amount)->toBe(400.0);
    $response->assertRedirect(route('frontdesk.facility-reservations.show', $reservation));
});

test('front desk staff without manage-facilities cannot create facility', function (): void {
    $this->role->permissions()->detach($this->facilityPerm->permission_id);

    $frontDeskOnlyUser = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($frontDeskOnlyUser)->get(route('frontdesk.facilities.index'));
    $response->assertForbidden();

    $responseCreate = $this->actingAs($frontDeskOnlyUser)->get(route('frontdesk.facilities.create'));
    $responseCreate->assertForbidden();
});
