<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\PosCategory;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Models\User;
use Database\Seeders\ChargeCodeSeeder;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\PosProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoffeeshopPosTest extends TestCase
{
    use RefreshDatabase;

    public function test_cafeteria_can_access_pos_dashboard(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ChargeCodeSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $cafeteria = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();

        $this->actingAs($cafeteria)
            ->get(route('coffeeshop.dashboard'))
            ->assertOk()
            ->assertSee('Coffee Shop Dashboard');
    }

    public function test_tab_checkout_decrements_inventory_and_creates_order(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ChargeCodeSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        $product = PosProduct::where('name', 'Beer')->firstOrFail();
        $startingStock = $product->stock_quantity;

        $this->actingAs($user);

        $tabResponse = $this->postJson(route('coffeeshop.api.tabs.store'), [
            'tab_name' => 'Wilson',
            'tab_type' => 'walk_in',
        ])->assertCreated();

        $tabId = $tabResponse->json('tab.tab_id');

        $this->postJson(route('coffeeshop.api.tabs.items.store', $tabId), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson(route('coffeeshop.api.tabs.close', $tabId), [
            'payment_method' => 'cash',
        ])->assertOk();

        $product->refresh();
        $this->assertSame($startingStock - 1, $product->stock_quantity);
        $this->assertDatabaseHas('pos_orders', [
            'customer_name' => 'Wilson',
            'status' => 'closed',
            'payment_method' => 'cash',
        ]);
    }

    public function test_product_search_returns_matching_items(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();

        $this->actingAs($user)
            ->getJson(route('coffeeshop.api.products.search', ['q' => 'San Miguel']))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Beer']);
    }

    public function test_non_stockable_product_bypasses_inventory_checks(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ChargeCodeSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        // Americano is seeded with stock_tracking = 'none' and stock_quantity = 0
        $product = PosProduct::where('name', 'Americano')->firstOrFail();

        $this->actingAs($user);

        $tabResponse = $this->postJson(route('coffeeshop.api.tabs.store'), [
            'tab_name' => 'NonStockableTest',
            'tab_type' => 'walk_in',
        ])->assertCreated();

        $tabId = $tabResponse->json('tab.tab_id');

        // Since it's none-tracked (made to order), adding it with 0 stock should succeed!
        $this->postJson(route('coffeeshop.api.tabs.items.store', $tabId), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ])->assertOk();

        // checkout
        $this->postJson(route('coffeeshop.api.tabs.close', $tabId), [
            'payment_method' => 'cash',
        ])->assertOk();

        $product->refresh();
        // stock should remain 0 (not tracked/decremented)
        $this->assertSame(0, $product->stock_quantity);
    }

    public function test_manual_stock_adjustment_logs_activity(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        $product = PosProduct::firstOrFail();

        $this->actingAs($user)
            ->post(route('coffeeshop.inventory.adjust', $product), [
                'adjustment_type' => 'restock',
                'quantity' => 10,
                'notes' => 'Test restock notes',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activitylogs', [
            'action_type' => 'RESTOCK_PRODUCT',
        ]);
    }

    public function test_product_edit_logs_descriptive_changes(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        $product = PosProduct::firstOrFail();

        $this->actingAs($user)
            ->put(route('coffeeshop.products.update', $product), [
                'category_id' => $product->category_id,
                'name' => 'Updated Drink Name',
                'price' => 250.00,
                'stock_quantity' => 50,
                'is_active' => 1,
                'is_stockable' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activitylogs', [
            'action_type' => 'EDIT_PRODUCT',
        ]);

        $log = ActivityLog::where('action_type', 'EDIT_PRODUCT')->orderByDesc('log_id')->first();
        $this->assertStringContainsString('name changed from', $log->description);
    }

    public function test_deactivated_category_products_are_hidden_in_pos(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        $category = PosCategory::where('name', 'Beer')->firstOrFail();
        $category->update(['is_active' => false]);

        $this->actingAs($user)
            ->getJson(route('coffeeshop.api.products.search', ['q' => 'San Miguel']))
            ->assertOk()
            ->assertJsonMissing(['name' => 'Beer']);
    }

    public function test_discount_cannot_exceed_subtotal(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ChargeCodeSeeder::class);
        $this->seed(PosCategorySeeder::class);
        $this->seed(PosProductSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
        $product = PosProduct::firstOrFail();

        $this->actingAs($user);

        $tabResponse = $this->postJson(route('coffeeshop.api.tabs.store'), [
            'tab_name' => 'DiscountClampTest',
            'tab_type' => 'walk_in',
        ])->assertCreated();

        $tabId = $tabResponse->json('tab.tab_id');

        $this->postJson(route('coffeeshop.api.tabs.items.store', $tabId), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ])->assertOk();

        // Apply flat discount of 1000.00 (which exceeds the subtotal).
        $this->postJson(route('coffeeshop.api.tabs.discount.apply', $tabId), [
            'discount_type' => 'VIP',
            'discount_amount' => 1000.00,
            'is_discount_percentage' => false,
        ])->assertStatus(422);

        // Apply percentage discount of 150%.
        $this->postJson(route('coffeeshop.api.tabs.discount.apply', $tabId), [
            'discount_type' => 'VIP',
            'discount_amount' => 150.00,
            'is_discount_percentage' => true,
        ])->assertStatus(422);

        // Apply exactly 100% percentage discount.
        $this->postJson(route('coffeeshop.api.tabs.discount.apply', $tabId), [
            'discount_type' => 'VIP',
            'discount_amount' => 100.00,
            'is_discount_percentage' => true,
        ])->assertOk();

        $tab = PosTab::findOrFail($tabId);
        $this->assertEquals(0.00, (float) $tab->total);
    }
}
