<?php

namespace Tests\Feature;

use App\Models\PosCategory;
use App\Models\PosProduct;
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
}
