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
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // 1. Setup Roles
    $this->accountingRole = Role::create([
        'role_name' => 'ACCOUNTING',
        'description' => 'Accounting Role',
        'is_active' => true,
    ]);

    // 2. Setup Permissions
    $viewDashboard = Permission::create([
        'permission_key' => 'view-accounting-dashboard',
        'description' => 'View accounting dashboard',
        'module' => 'Accounting',
        'is_active' => true,
    ]);

    $manageBilling = Permission::create([
        'permission_key' => 'manage-accounting-billing',
        'description' => 'Manage accounting billing',
        'module' => 'Accounting',
        'is_active' => true,
    ]);

    $managePayments = Permission::create([
        'permission_key' => 'manage-accounting-payments',
        'description' => 'Manage accounting payments',
        'module' => 'Accounting',
        'is_active' => true,
    ]);

    $manageExpenses = Permission::create([
        'permission_key' => 'manage-accounting-expenses',
        'description' => 'Manage accounting expenses',
        'module' => 'Accounting',
        'is_active' => true,
    ]);

    $this->accountingRole->permissions()->sync([
        $viewDashboard->permission_id,
        $manageBilling->permission_id,
        $managePayments->permission_id,
        $manageExpenses->permission_id,
    ]);

    // 3. Create User
    $this->accountantUser = User::factory()->create([
        'username' => 'accountant_test',
        'full_name' => 'Finance Person',
        'role_id' => $this->accountingRole->role_id,
        'is_active' => true,
    ]);

    // 4. Create dummy ChargeCodes (General payments/room charges)
    $this->roomChargeCode = ChargeCode::create([
        'charge_code' => 100,
        'description' => 'ROOM CHARGE',
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
        'user_id' => $this->accountantUser->user_id,
        'start_time' => now(),
    ]);
});

test('unauthenticated accounting users are redirected to login', function (): void {
    $this->get(route('accounting.dashboard'))
        ->assertRedirect('/');
});

test('authenticated accountants can view the accounting dashboard', function (): void {
    // Create guest, folio, and transaction
    $guest = Guest::create([
        'last_name' => 'Doe',
        'first_name' => 'John',
    ]);

    $folio = Folio::create([
        'folio_number' => 'F-001',
        'guest_id' => $guest->guest_id,
        'status' => 'OPEN',
    ]);

    Transaction::create([
        'folio_id' => $folio->folio_id,
        'charge_code' => $this->roomChargeCode->charge_code,
        'shift_id' => $this->shift->shift_id,
        'user_id' => $this->accountantUser->user_id,
        'transaction_date' => now()->toDateString(),
        'charge_amount' => 5000.00,
        'credit_amount' => 0.00,
        'payment_method' => 'NONE',
    ]);

    $response = $this->actingAs($this->accountantUser)
        ->get(route('accounting.dashboard'));

    $response->assertOk();
    $response->assertSee('5,000.00'); // Check dynamic revenue display
});

test('accountant can list and filter invoices/billing', function (): void {
    $guest = Guest::create(['last_name' => 'Smith', 'first_name' => 'Anna']);
    $folio = Folio::create(['folio_number' => 'F-999', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    $response = $this->actingAs($this->accountantUser)
        ->get(route('accounting.billing', ['date_range' => 'all']));

    $response->assertOk();
    $response->assertSee('F-999');
    $response->assertSee('Anna Smith');
});

test('accountant can view folio details page when booking arrival or departure date is null', function (): void {
    $guest = Guest::create(['last_name' => 'NullDate', 'first_name' => 'Test']);
    $folio = Folio::create(['folio_number' => 'F-888', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);
    $room = Room::create(['room_number' => '101', 'room_type' => 'Standard', 'base_rate' => 1500.00, 'status' => 'OCCUPIED']);
    Booking::create([
        'folio_id' => $folio->folio_id,
        'room_id' => $room->room_id,
        'arrival_date' => now()->toDateString(),
        'departure_date' => null,
        'status' => 'RESERVED',
    ]);

    $response = $this->actingAs($this->accountantUser)
        ->get(route('accounting.billing.show', $folio->folio_id));

    $response->assertOk();
    $response->assertSee('F-888');
    $response->assertSee('NullDate');
});

test('accountant can view folio details/invoice page', function (): void {
    $guest = Guest::create(['last_name' => 'Santos', 'first_name' => 'Maria']);
    $folio = Folio::create(['folio_number' => 'F-777', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    $response = $this->actingAs($this->accountantUser)
        ->get(route('accounting.billing.show', $folio->folio_id));

    $response->assertOk();
    $response->assertSee('F-777');
    $response->assertSee('Santos');
});

test('accountant can record payment', function (): void {
    $guest = Guest::create(['last_name' => 'Taylor', 'first_name' => 'Elizabeth']);
    $folio = Folio::create(['folio_number' => 'F-333', 'guest_id' => $guest->guest_id, 'status' => 'OPEN']);

    $response = $this->actingAs($this->accountantUser)
        ->post(route('accounting.payments.store'), [
            'folio_id' => $folio->folio_id,
            'payment_method' => 'CASH',
            'amount' => 1500.00,
            'reference_notes' => 'OR-9912',
        ]);

    $response->assertRedirect(route('accounting.payments'));

    // Check if the payment transaction is stored in DB
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'credit_amount' => 1500.00,
        'payment_method' => 'CASH',
        'charge_number' => 'OR-9912',
    ]);
});

test('accountant can list and add operating expenses', function (): void {
    $response = $this->actingAs($this->accountantUser)
        ->post(route('accounting.expenses.store'), [
            'expense_date' => now()->toDateString(),
            'department' => 'Maintenance',
            'purpose' => 'Light Bulb replacements',
            'category' => 'Supplies',
            'amount' => 650.00,
            'funding_source' => 'FRONT DESK',
            'requested_by' => 'John Doe',
        ]);

    $response->assertRedirect(route('accounting.expenses'));

    $this->assertDatabaseHas('expenses', [
        'department' => 'Maintenance',
        'purpose' => 'Light Bulb replacements',
        'amount' => 650.00,
        'status' => 'APPROVED',
    ]);

    $getResponse = $this->actingAs($this->accountantUser)
        ->get(route('accounting.expenses'));

    $getResponse->assertOk();
    $getResponse->assertSee('Light Bulb replacements');
});
