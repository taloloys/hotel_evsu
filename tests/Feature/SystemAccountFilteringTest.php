<?php

use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Create Role
    $this->role = Role::create([
        'role_name' => 'FRONTDESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    // Attach all operational permissions
    $permissions = [
        Permission::create(['permission_key' => 'view-guest-list', 'description' => 'View guest list', 'module' => 'Front Desk', 'is_active' => true]),
        Permission::create(['permission_key' => 'view-guest-folio', 'description' => 'View guest folio', 'module' => 'Front Desk', 'is_active' => true]),
        Permission::create(['permission_key' => 'view-shift-sales', 'description' => 'View shift sales', 'module' => 'Front Desk', 'is_active' => true]),
        Permission::create(['permission_key' => 'manage-reservations', 'description' => 'Manage reservations', 'module' => 'Front Desk', 'is_active' => true]),
    ];

    $this->role->permissions()->sync(collect($permissions)->pluck('permission_id'));

    // Create User
    $this->user = User::factory()->create([
        'username' => 'frontdesk_agent',
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);

    // Create a regular guest and system guest
    $this->realGuest = Guest::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'contact_number' => '12345',
        'guest_type' => 'GUEST',
    ]);

    $this->systemGuest = Guest::create([
        'first_name' => 'POS',
        'last_name' => 'WALK-IN',
        'contact_number' => 'N/A',
        'guest_type' => 'SYSTEM',
    ]);

    // Create a regular folio and system folio
    $this->realFolio = Folio::create([
        'folio_number' => 'REG-2026001',
        'guest_id' => $this->realGuest->guest_id,
        'folio_type' => 'GUEST',
        'status' => 'OPEN',
    ]);

    $this->systemFolio = Folio::create([
        'folio_number' => 'POS-WALKIN',
        'guest_id' => $this->systemGuest->guest_id,
        'folio_type' => 'SYSTEM',
        'status' => 'OPEN',
    ]);

    // Setup active shift and charge code for reports test
    $this->shift = Shift::create([
        'user_id' => $this->user->user_id,
        'start_time' => now(),
    ]);

    $this->chargeCode = ChargeCode::create([
        'charge_code' => 200,
        'description' => 'F&B CHARGES',
        'category' => 'RESTAURANT',
        'is_active' => true,
    ]);
});

test('operational guest list excludes system guest accounts', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.guest-list'));

    $response->assertOk();
    $response->assertSee('Doe, John');
    $response->assertDontSee('WALK-IN, POS');
});

test('operational guest search excludes system guest accounts', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.guests.search', ['q' => 'POS']));

    $response->assertOk();
    $response->assertJsonCount(0);

    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.guests.search', ['q' => 'John']));

    $response->assertOk();
    $response->assertJsonCount(1);
    $response->assertJsonFragment(['first_name' => 'John']);
});

test('operational guest folio list excludes system folios', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.guest-folio'));

    $response->assertOk();
    $response->assertSee('REG-2026001');
    $response->assertDontSee('POS-WALKIN');
});

test('operational reservation view excludes system guest selection', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.reservation'));

    $response->assertOk();
    $response->assertDontSee('WALK-IN, POS');
});

test('financial reporting includes transactions on system folios', function (): void {
    // Post a transaction to the system folio
    Transaction::create([
        'folio_id' => $this->systemFolio->folio_id,
        'charge_code' => $this->chargeCode->charge_code,
        'shift_id' => $this->shift->shift_id,
        'user_id' => $this->user->user_id,
        'transaction_date' => now()->toDateString(),
        'charge_number' => 'POS-12345',
        'payment_method' => 'NONE',
        'reference_notes' => 'F&B walk-in sale',
        'charge_amount' => 150.00,
        'credit_amount' => 0.00,
    ]);

    // View shift sales
    $response = $this->actingAs($this->user)
        ->get(route('frontdesk.shift-sales', [
            'report_type' => 'restaurant',
            'date_from' => now()->toDateString(),
            'shift_id' => $this->shift->shift_id,
        ]));

    $response->assertOk();
    $response->assertSee('150.00');
});
