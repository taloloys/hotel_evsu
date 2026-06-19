<?php

use App\Models\ChargeCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Setup admin role
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    // Create admin user
    $this->adminUser = User::factory()->create([
        'role_id' => $this->adminRole->role_id,
        'is_active' => true,
    ]);

    // Create default charge codes
    $this->roomCharge = ChargeCode::create([
        'charge_code' => 100,
        'description' => 'ROOM CHARGE',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    $this->govTax = ChargeCode::create([
        'charge_code' => 101,
        'description' => 'GOVERNMENT TAX',
        'category' => 'TAX_SERVICE',
        'is_active' => true,
    ]);

    $this->inactiveCharge = ChargeCode::create([
        'charge_code' => 300,
        'description' => 'OLD SERVICE',
        'category' => 'HOTEL',
        'is_active' => false,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.chargecodes'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view the charge codes management page with stats', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.chargecodes'));

    $response->assertOk();
    $response->assertSee('ROOM CHARGE');
    $response->assertSee('[100]');
    $response->assertSee('GOVERNMENT TAX');
    $response->assertSee('[101]');
    $response->assertSee('OLD SERVICE');
    $response->assertSee('[300]');

    // Verification of stats
    $response->assertSee('3'); // Total
    $response->assertSee('2'); // Active
    $response->assertSee('1'); // Inactive
});

test('admin can create a new charge code with valid data', function (): void {
    $chargeData = [
        'charge_code' => 200,
        'description' => 'RESTAURANT CHARGE',
        'category' => 'RESTAURANT',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.chargecodes.store'), $chargeData);

    $response->assertRedirect(route('admin.chargecodes'));
    $response->assertSessionHas('success', 'Charge code created successfully.');

    $this->assertDatabaseHas('chargecodes', [
        'charge_code' => 200,
        'description' => 'RESTAURANT CHARGE',
        'category' => 'RESTAURANT',
        'is_active' => true,
    ]);
});

test('charge code creation fails if code already exists', function (): void {
    $chargeData = [
        'charge_code' => 100, // Pre-existing
        'description' => 'DUPLICATE ROOM CHARGE',
        'category' => 'HOTEL',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.chargecodes.store'), $chargeData);

    $response->assertSessionHasErrors(['charge_code']);
});

test('admin can update an existing charge code description and category', function (): void {
    $updateData = [
        'description' => 'UPDATED ROOM CHARGE',
        'category' => 'HOTEL',
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.chargecodes.update', $this->roomCharge), $updateData);

    $response->assertRedirect(route('admin.chargecodes'));
    $response->assertSessionHas('success', 'Charge code updated successfully.');

    $this->assertDatabaseHas('chargecodes', [
        'charge_code' => 100,
        'description' => 'UPDATED ROOM CHARGE',
        'category' => 'HOTEL',
    ]);
});

test('admin can toggle active status of a charge code', function (): void {
    // Disable active charge code
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.chargecodes.toggle', $this->roomCharge));

    $response->assertRedirect(route('admin.chargecodes'));
    $response->assertSessionHas('success', 'Charge code [100] has been successfully disabled.');
    expect($this->roomCharge->fresh()->is_active)->toBeFalse();

    // Enable inactive charge code
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.chargecodes.toggle', $this->roomCharge));

    $response->assertRedirect(route('admin.chargecodes'));
    $response->assertSessionHas('success', 'Charge code [100] has been successfully enabled.');
    expect($this->roomCharge->fresh()->is_active)->toBeTrue();
});
