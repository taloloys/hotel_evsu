<?php

use App\Models\CreditAccount;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\ChargeCodeSeeder;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\PosProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(ChargeCodeSeeder::class);
    $this->seed(PosCategorySeeder::class);
    $this->seed(PosProductSeeder::class);

    $this->cashier = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
    $this->product = PosProduct::where('stock_tracking', 'manual')->first() ?? PosProduct::firstOrFail();

    // Ensure open shift exists
    Shift::create([
        'user_id' => $this->cashier->user_id,
        'shift_date' => now()->toDateString(),
        'start_time' => now(),
        'opened_at' => now(),
        'starting_cash' => 1000,
        'status' => 'open',
    ]);
});

test('cafeteria pos prevents closing tab when vip credit account exceeds limit', function (): void {
    $account = CreditAccount::create([
        'account_name' => 'VIP Test Corp',
        'credit_limit' => 100.00,
        'is_active' => true,
    ]);

    $tab = PosTab::create([
        'tab_name' => 'VIP Tab',
        'tab_type' => 'account',
        'credit_account_id' => $account->account_id,
        'status' => 'open',
        'subtotal' => 150.00,
        'total' => 150.00,
        'opened_by' => $this->cashier->user_id,
        'opened_at' => now(),
    ]);

    $tab->items()->create([
        'product_id' => $this->product->product_id,
        'quantity' => 1,
        'unit_price' => 150.00,
        'line_total' => 150.00,
    ]);

    $response = $this->actingAs($this->cashier)
        ->postJson(route('coffeeshop.api.tabs.close', $tab->tab_id), [
            'payment_method' => 'account_charge',
            'credit_account_id' => $account->account_id,
        ]);

    $response->assertStatus(422)
        ->assertJsonFragment([
            'message' => "Charge amount (150) exceeds available credit ({$account->available_credit}).",
        ]);
});

test('cafeteria pos allows closing tab when vip credit account is within limit', function (): void {
    $account = CreditAccount::create([
        'account_name' => 'VIP Solvent Corp',
        'credit_limit' => 500.00,
        'is_active' => true,
    ]);

    $tab = PosTab::create([
        'tab_name' => 'Solvent VIP Tab',
        'tab_type' => 'account',
        'credit_account_id' => $account->account_id,
        'status' => 'open',
        'subtotal' => 200.00,
        'total' => 200.00,
        'opened_by' => $this->cashier->user_id,
        'opened_at' => now(),
    ]);

    $tab->items()->create([
        'product_id' => $this->product->product_id,
        'quantity' => 1,
        'unit_price' => 200.00,
        'line_total' => 200.00,
    ]);

    $response = $this->actingAs($this->cashier)
        ->postJson(route('coffeeshop.api.tabs.close', $tab->tab_id), [
            'payment_method' => 'account_charge',
            'credit_account_id' => $account->account_id,
        ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Tab closed and order completed.');
});
