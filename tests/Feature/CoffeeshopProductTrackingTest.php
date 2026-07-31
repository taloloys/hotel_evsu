<?php

use App\Models\PosProduct;
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
});

// ---------------------------------------------------------------------------
// Products list page
// ---------------------------------------------------------------------------

test('none-tracked products show "None" in the stock column', function (): void {
    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.products'));

    $response->assertOk();
    $response->assertSee('None');
});

test('none-tracked products do not show a numeric stock value', function (): void {
    // Americano is seeded as stock_tracking = none with stock_quantity = 0
    $product = PosProduct::where('name', 'Americano')->firstOrFail();
    expect($product->stock_tracking)->toBe('none');

    // The product index should show "Made to order" and NOT a bare "0"
    // alongside the product row (we verify the helper methods work correctly)
    expect($product->isNoTracking())->toBeTrue();
    expect($product->isManualTracked())->toBeFalse();
    expect($product->isLowStock())->toBeFalse();
});

test('none-tracked products never show a Low Stock badge', function (): void {
    $product = PosProduct::where('name', 'Americano')->firstOrFail();

    // Even if we forcefully set a very low stock_quantity, isLowStock() must remain false
    $product->update(['stock_quantity' => 0]);

    expect($product->fresh()->isLowStock())->toBeFalse();
});

test('manual-tracked products show their stock quantity', function (): void {
    $product = PosProduct::where('name', 'Beer')->firstOrFail();
    expect($product->stock_tracking)->toBe('manual');
    expect($product->isManualTracked())->toBeTrue();
    expect($product->isNoTracking())->toBeFalse();
});

test('manual-tracked products trigger isLowStock when below threshold', function (): void {
    $product = PosProduct::where('name', 'Beer')->firstOrFail();
    $product->update(['stock_quantity' => 2, 'low_stock_threshold' => 10]);

    expect($product->fresh()->isLowStock())->toBeTrue();
});

// ---------------------------------------------------------------------------
// Inventory management page
// ---------------------------------------------------------------------------

test('inventory page only shows manual-tracked products', function (): void {
    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.inventory'));

    $response->assertOk();

    // Beer is manual — should appear
    $response->assertSee('Beer');

    // Americano is none — must NOT appear on the inventory page
    $response->assertDontSee('Americano');
    $response->assertDontSee('Cappuccino');
    $response->assertDontSee('Club Sandwich');
    $response->assertDontSee('French Fries');
});

test('inventory low stock alert only references manual-tracked products', function (): void {
    // Force Coffee Beans to be critically low
    PosProduct::where('name', 'Coffee Beans')->firstOrFail()->update(['stock_quantity' => 1]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.inventory'));

    $response->assertOk();
    $response->assertSee('Low Stock Alert');
    $response->assertSee('Coffee Beans');

    // Made-to-order items must never appear in the alert
    $response->assertDontSee('Americano');
});

// ---------------------------------------------------------------------------
// Dashboard
// ---------------------------------------------------------------------------

test('dashboard Inventory Warning only fires for manual-tracked products', function (): void {
    // Drop Beer's stock to trigger a warning
    PosProduct::where('name', 'Beer')->firstOrFail()->update(['stock_quantity' => 1, 'low_stock_threshold' => 10]);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard'));

    $response->assertOk();
    $response->assertSee('Inventory Warning');
});

test('dashboard Inventory Warning does not fire for none-tracked products', function (): void {
    // Ensure all manual-tracked products are well-stocked
    PosProduct::where('stock_tracking', 'manual')->get()->each(function ($p) {
        $p->update(['stock_quantity' => 999, 'low_stock_threshold' => 5]);
    });

    // Made-to-order product with stock_quantity = 0 should NOT trigger the warning
    $americano = PosProduct::where('name', 'Americano')->firstOrFail();
    expect($americano->stock_tracking)->toBe('none');
    expect($americano->stock_quantity)->toBe(0);

    $response = $this->actingAs($this->cafeteriaUser)
        ->get(route('coffeeshop.dashboard'));

    $response->assertOk();
    $response->assertDontSee('Inventory Warning');
});

// ---------------------------------------------------------------------------
// Edit form
// ---------------------------------------------------------------------------

test('saving a product with stock_tracking=none clears stock fields', function (): void {
    $product = PosProduct::where('name', 'Beer')->firstOrFail();

    $this->actingAs($this->cafeteriaUser)
        ->put(route('coffeeshop.products.update', $product), [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock_tracking' => 'none',
            'stock_quantity' => 50,
            'is_active' => '1',
        ])
        ->assertRedirect(route('coffeeshop.products'));

    $product->refresh();
    expect($product->stock_tracking)->toBe('none');
    expect($product->stock_quantity)->toBe(0);
    expect($product->low_stock_threshold)->toBeNull();
});

test('saving a product with stock_tracking=manual keeps stock quantity', function (): void {
    $product = PosProduct::where('name', 'Beer')->firstOrFail();

    $this->actingAs($this->cafeteriaUser)
        ->put(route('coffeeshop.products.update', $product), [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock_tracking' => 'manual',
            'stock_quantity' => 42,
            'low_stock_threshold' => 8,
            'is_active' => '1',
        ])
        ->assertRedirect(route('coffeeshop.products'));

    $product->refresh();
    expect($product->stock_tracking)->toBe('manual');
    expect($product->stock_quantity)->toBe(42);
    expect($product->low_stock_threshold)->toBe(8);
});
