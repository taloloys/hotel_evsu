<?php

use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosProduct;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\PosProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(PosCategorySeeder::class);
    $this->seed(PosProductSeeder::class);

    $this->cafeteriaUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();

    // Create a shift to satisfy foreign key constraint on pos_orders
    $this->shift = Shift::create([
        'user_id' => $this->cafeteriaUser->user_id,
        'start_time' => now(),
    ]);
});

test('cafeteria staff can access the dashboard and see slideshow stats', function (): void {
    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard'));

    $response->assertOk();
    $response->assertSee('Featured Product');
    $response->assertSee('Best Sellers');
    $response->assertSee('Sales Overview');
    $response->assertSee('Inventory Overview');
    $response->assertSee('Today');
    $response->assertSee('Weekly');
    $response->assertSee('Monthly');
});

test('featured product details are correctly computed and displayed', function (): void {
    $product = PosProduct::where('name', 'Americano')->firstOrFail();

    $order = PosOrder::create([
        'order_number' => 'ORD-TEST-100',
        'customer_name' => 'John Doe',
        'status' => 'closed',
        'payment_method' => 'cash',
        'subtotal' => 120.00,
        'total' => 120.00,
        'user_id' => $this->cafeteriaUser->user_id,
        'shift_id' => $this->shift->shift_id,
        'closed_at' => now(),
    ]);

    PosOrderItem::create([
        'order_id' => $order->order_id,
        'product_id' => $product->product_id,
        'product_name' => $product->name,
        'quantity' => 3,
        'unit_price' => 120.00,
        'line_total' => 360.00,
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard'));

    $response->assertOk();
    $response->assertSee('Americano');
    $response->assertSee('Beverage');
    $response->assertSee('360.00');
});

test('recent orders partial endpoint returns HTML rows', function (): void {
    PosOrder::create([
        'order_number' => 'ORD-TEST-999',
        'customer_name' => 'Alice Margatroid',
        'status' => 'closed',
        'payment_method' => 'cash',
        'subtotal' => 90.00,
        'total' => 90.00,
        'user_id' => $this->cafeteriaUser->user_id,
        'shift_id' => $this->shift->shift_id,
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard.recent-orders-partial'));

    $response->assertOk();
    $response->assertSee('ORD-TEST-999');
    $response->assertSee('Alice Margatroid');
});

test('inventory warning alert is displayed when a product is low stock', function (): void {
    $product = PosProduct::where('name', 'Latte')->firstOrFail();
    $product->update([
        'stock_quantity' => 2,
        'low_stock_threshold' => 10,
    ]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard'));

    $response->assertOk();
    $response->assertSee('Inventory Warning');
    $response->assertSee('Restock Now');
});
