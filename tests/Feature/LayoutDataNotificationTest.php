<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    $this->authorizedUser = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);

    $this->facility = Facility::create([
        'name' => 'Function Hall Alpha',
        'rate' => 1200.00,
        'rate_type' => 'hourly',
        'hourly_rate' => 1200.00,
        'is_active' => true,
    ]);
});

test('layout data includes pending facility reservations and pending count for authorized user', function (): void {
    $reservation = FacilityReservation::create([
        'facility_id' => $this->facility->facility_id,
        'booker_name' => 'Maria Santos',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'event_name' => 'Tech Summit 2026',
        'reservation_date' => Carbon::tomorrow()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'status' => 'pending',
        'estimated_amount' => 9600.00,
    ]);

    $response = $this->actingAs($this->authorizedUser)
        ->getJson(route('api.layout-data'));

    $response->assertOk()
        ->assertJsonStructure([
            'notifications',
            'pendingFacilityCount',
        ]);

    expect($response->json('pendingFacilityCount'))->toBe(1);

    $notifications = collect($response->json('notifications'));
    $facilityNotif = $notifications->firstWhere('id', 'facility-pending-'.$reservation->reservation_id);

    expect($facilityNotif)->not->toBeNull();
    expect($facilityNotif['type'])->toBe('facility_reservation');
    expect($facilityNotif['severity'])->toBe('warning');
    expect($facilityNotif['message'])->toContain('Function Hall Alpha');
    expect($facilityNotif['message'])->toContain('Maria Santos');
    expect($facilityNotif['link'])->toBe(route('frontdesk.facility-reservations.show', $reservation));
});

test('layout data includes approved facility reservations scheduled for today', function (): void {
    $reservation = FacilityReservation::create([
        'facility_id' => $this->facility->facility_id,
        'booker_name' => 'Juan Dela Cruz',
        'booker_email' => 'juan@example.com',
        'booker_contact' => '09987654321',
        'event_name' => 'Executive Meeting',
        'reservation_date' => Carbon::today()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '17:00:00',
        'status' => 'approved',
        'estimated_amount' => 4800.00,
    ]);

    $response = $this->actingAs($this->authorizedUser)
        ->getJson(route('api.layout-data'));

    $response->assertOk();

    $notifications = collect($response->json('notifications'));
    $facilityNotif = $notifications->firstWhere('id', 'facility-today-'.$reservation->reservation_id);

    expect($facilityNotif)->not->toBeNull();
    expect($facilityNotif['type'])->toBe('facility_reservation');
    expect($facilityNotif['severity'])->toBe('info');
    expect($facilityNotif['message'])->toContain('Function Hall Alpha');
    expect($facilityNotif['message'])->toContain('Juan Dela Cruz');
});

test('layout data includes active facility reservations concluding today', function (): void {
    $reservation = FacilityReservation::create([
        'facility_id' => $this->facility->facility_id,
        'booker_name' => 'Ana Reyes',
        'booker_email' => 'ana@example.com',
        'booker_contact' => '09112233445',
        'event_name' => 'Workshop Day 2',
        'reservation_date' => Carbon::today()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '16:00:00',
        'status' => 'active',
        'estimated_amount' => 9600.00,
    ]);

    $response = $this->actingAs($this->authorizedUser)
        ->getJson(route('api.layout-data'));

    $response->assertOk();

    $notifications = collect($response->json('notifications'));
    $facilityNotif = $notifications->firstWhere('id', 'facility-active-'.$reservation->reservation_id);

    expect($facilityNotif)->not->toBeNull();
    expect($facilityNotif['type'])->toBe('facility_reservation');
    expect($facilityNotif['severity'])->toBe('warning');
    expect($facilityNotif['message'])->toContain('Ana Reyes');
    expect($facilityNotif['time'])->toBe('Action required');
});

test('user without facility permissions does not receive facility notifications', function (): void {
    $guestRole = Role::firstOrCreate(
        ['role_name' => 'GUEST'],
        ['description' => 'Guest User', 'is_active' => true]
    );

    $unauthorizedUser = User::factory()->create([
        'role_id' => $guestRole->role_id,
        'is_active' => true,
    ]);

    FacilityReservation::create([
        'facility_id' => $this->facility->facility_id,
        'booker_name' => 'Maria Santos',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'event_name' => 'Tech Summit 2026',
        'reservation_date' => Carbon::tomorrow()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'status' => 'pending',
        'estimated_amount' => 9600.00,
    ]);

    $response = $this->actingAs($unauthorizedUser)
        ->getJson(route('api.layout-data'));

    $response->assertOk();
    expect($response->json('pendingFacilityCount'))->toBe(0);

    $notifications = collect($response->json('notifications'));
    $facilityNotifs = $notifications->where('type', 'facility_reservation');
    expect($facilityNotifs)->toBeEmpty();
});
