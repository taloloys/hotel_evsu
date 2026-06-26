<?php

namespace Database\Seeders;

use App\Models\Folio;
use App\Models\Guest;
use App\Models\PosCategory;
use App\Models\PosProduct;
use App\Models\PosSetting;
use Illuminate\Database\Seeder;

class PosProductSeeder extends Seeder
{
    public function run(): void
    {
        $coffee = PosCategory::where('name', 'Coffee')->first();
        $tea = PosCategory::where('name', 'Tea')->first();
        $beer = PosCategory::where('name', 'Beer')->first();
        $food = PosCategory::where('name', 'Food')->first();
        $dessert = PosCategory::where('name', 'Dessert')->first();

        $products = [
            ['category_id' => $coffee?->category_id, 'name' => 'Americano', 'description' => 'Classic black coffee', 'price' => 120, 'stock_quantity' => 100, 'low_stock_threshold' => 20],
            ['category_id' => $coffee?->category_id, 'name' => 'Latte', 'description' => 'Espresso with steamed milk', 'price' => 150, 'stock_quantity' => 80, 'low_stock_threshold' => 15],
            ['category_id' => $coffee?->category_id, 'name' => 'Cappuccino', 'description' => 'Espresso with foamed milk', 'price' => 140, 'stock_quantity' => 75, 'low_stock_threshold' => 15],
            ['category_id' => $tea?->category_id, 'name' => 'Green Tea', 'description' => 'Hot green tea', 'price' => 90, 'stock_quantity' => 60, 'low_stock_threshold' => 10],
            ['category_id' => $beer?->category_id, 'name' => 'Beer', 'description' => 'San Miguel Pale Pilsen', 'price' => 95, 'stock_quantity' => 48, 'low_stock_threshold' => 12],
            ['category_id' => $food?->category_id, 'name' => 'Club Sandwich', 'description' => 'Triple-decker club sandwich', 'price' => 220, 'stock_quantity' => 30, 'low_stock_threshold' => 8],
            ['category_id' => $food?->category_id, 'name' => 'French Fries', 'description' => 'Crispy golden fries', 'price' => 110, 'stock_quantity' => 40, 'low_stock_threshold' => 10],
            ['category_id' => $dessert?->category_id, 'name' => 'Cookies', 'description' => 'Fresh baked cookies', 'price' => 90, 'stock_quantity' => 5, 'low_stock_threshold' => 10],
            ['category_id' => $coffee?->category_id, 'name' => 'Coffee Beans', 'description' => 'Arabica beans 1kg pack', 'price' => 450, 'stock_quantity' => 3, 'low_stock_threshold' => 5],
            ['category_id' => $coffee?->category_id, 'name' => 'Fresh Milk', 'description' => 'Fresh milk 1 liter', 'price' => 85, 'stock_quantity' => 8, 'low_stock_threshold' => 10],
        ];

        foreach ($products as $product) {
            if (! $product['category_id']) {
                continue;
            }

            PosProduct::firstOrCreate(
                ['name' => $product['name']],
                array_merge($product, ['is_active' => true])
            );
        }

        PosSetting::set('default_low_stock_threshold', '10');

        $walkInGuest = Guest::firstOrCreate(
            ['last_name' => 'WALK-IN', 'first_name' => 'POS'],
            ['contact_number' => 'N/A', 'guest_type' => 'SYSTEM']
        );

        $walkInFolio = Folio::firstOrCreate(
            ['folio_number' => 'POS-WALKIN'],
            [
                'guest_id' => $walkInGuest->guest_id,
                'folio_type' => 'SYSTEM',
                'status' => 'OPEN',
                'market_segment' => 'NONE',
                'symbol' => 'POS',
            ]
        );

        PosSetting::set('walk_in_folio_id', (string) $walkInFolio->folio_id);
    }
}
