<?php

use App\Models\Folio;
use App\Models\Guest;
use App\Models\PosCategory;
use App\Models\PosOrder;
use App\Models\PosProduct;
use App\Models\PosSetting;
use App\Models\PosTab;
use App\Models\PosTabItem;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\ChargeCodeSeeder;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(ChargeCodeSeeder::class);
    $this->seed(PosCategorySeeder::class);

    $this->cafeteriaUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();

    $this->shift = Shift::create([
        'user_id' => $this->cafeteriaUser->user_id,
        'start_time' => now(),
    ]);

    $category = PosCategory::first();
    $this->product = PosProduct::create([
        'category_id' => $category->category_id,
        'name' => 'Test Coffee',
        'price' => 100.00,
        'stock_quantity' => 2,
        'low_stock_threshold' => 5,
        'is_stockable' => true,
        'is_active' => true,
    ]);
    $guest = Guest::create(['last_name' => 'Walkin', 'first_name' => 'Guest']);
    $folio = Folio::create([
        'folio_number' => 'F-WALKIN',
        'guest_id' => $guest->guest_id,
        'status' => 'OPEN',
    ]);
    PosSetting::set('walk_in_folio_id', (string) $folio->folio_id);
});

test('pos tab can be closed with gcash payment method', function (): void {
    $tab = PosTab::create([
        'tab_name' => 'Walk-in Guest',
        'tab_type' => 'walk_in',
        'status' => 'open',
        'subtotal' => 100.00,
        'total' => 100.00,
        'opened_by' => $this->cafeteriaUser->user_id,
    ]);

    PosTabItem::create([
        'tab_id' => $tab->tab_id,
        'product_id' => $this->product->product_id,
        'quantity' => 1,
        'unit_price' => 100.00,
        'line_total' => 100.00,
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->postJson(route('coffeeshop.api.tabs.close', $tab->tab_id), [
            'payment_method' => 'gcash',
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('pos_orders', [
        'tab_id' => $tab->tab_id,
        'payment_method' => 'gcash',
    ]);
});

test('pos tab can be closed with card payment method', function (): void {
    $tab = PosTab::create([
        'tab_name' => 'Walk-in Card Guest',
        'tab_type' => 'walk_in',
        'status' => 'open',
        'subtotal' => 100.00,
        'total' => 100.00,
        'opened_by' => $this->cafeteriaUser->user_id,
    ]);

    PosTabItem::create([
        'tab_id' => $tab->tab_id,
        'product_id' => $this->product->product_id,
        'quantity' => 1,
        'unit_price' => 100.00,
        'line_total' => 100.00,
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->postJson(route('coffeeshop.api.tabs.close', $tab->tab_id), [
            'payment_method' => 'card',
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('pos_orders', [
        'tab_id' => $tab->tab_id,
        'payment_method' => 'card',
    ]);
});

test('inventory filter low_stock returns products below low stock threshold', function (): void {
    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.inventory', ['filter' => 'low_stock']));

    $response->assertOk();
    $response->assertSee('Test Coffee');
});

test('orders page displays gcash and card payment method badges correctly', function (): void {
    PosOrder::create([
        'order_number' => 'ORD-TEST-GCASH',
        'customer_name' => 'GCash Customer',
        'status' => 'closed',
        'payment_method' => 'gcash',
        'subtotal' => 100.00,
        'total' => 100.00,
        'user_id' => $this->cafeteriaUser->user_id,
        'shift_id' => $this->shift->shift_id,
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.orders'));

    $response->assertOk();
    $response->assertSee('GCASH');
});

test('layout data generates low stock notification link with search parameter', function (): void {
    $response = $this->actingAs($this->cafeteriaUser)
        ->getJson(route('api.layout-data'));

    $response->assertOk();
    $data = $response->json();
    $lowStockNotif = collect($data['notifications'])->firstWhere('id', 'inventory-low-'.$this->product->product_id);

    expect($lowStockNotif)->not->toBeNull();
    expect($lowStockNotif['link'])->toContain('search=Test');

    $targetResponse = $this->actingAs($this->cafeteriaUser)->get($lowStockNotif['link']);
    $targetResponse->assertOk();
    $targetResponse->assertSee('Test Coffee');
});
