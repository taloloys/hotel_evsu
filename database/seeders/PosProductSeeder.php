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
            // Made-to-order items — no stock tracking
            ['category_id' => $coffee?->category_id, 'name' => 'Americano', 'description' => 'Classic black coffee', 'price' => 120, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $coffee?->category_id, 'name' => 'Latte', 'description' => 'Espresso with steamed milk', 'price' => 150, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $coffee?->category_id, 'name' => 'Cappuccino', 'description' => 'Espresso with foamed milk', 'price' => 140, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $tea?->category_id, 'name' => 'Green Tea', 'description' => 'Hot green tea', 'price' => 90, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $food?->category_id, 'name' => 'Club Sandwich', 'description' => 'Triple-decker club sandwich', 'price' => 220, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $food?->category_id, 'name' => 'French Fries', 'description' => 'Crispy golden fries', 'price' => 110, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $dessert?->category_id, 'name' => 'Cookies', 'description' => 'Fresh baked cookies', 'price' => 90, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],
            ['category_id' => $dessert?->category_id, 'name' => 'Donut', 'description' => 'Freshly made donut', 'price' => 70, 'stock_quantity' => 0, 'low_stock_threshold' => null, 'stock_tracking' => 'none'],

            // Physically stocked items — manual tracking
            ['category_id' => $beer?->category_id, 'name' => 'Beer', 'description' => 'San Miguel Pale Pilsen', 'price' => 95, 'stock_quantity' => 48, 'low_stock_threshold' => 12, 'stock_tracking' => 'manual'],
            ['category_id' => $coffee?->category_id, 'name' => 'Coffee Beans', 'description' => 'Arabica beans 1kg pack', 'price' => 450, 'stock_quantity' => 3, 'low_stock_threshold' => 5, 'stock_tracking' => 'manual'],
            ['category_id' => $coffee?->category_id, 'name' => 'Fresh Milk', 'description' => 'Fresh milk 1 liter', 'price' => 85, 'stock_quantity' => 8, 'low_stock_threshold' => 10, 'stock_tracking' => 'manual'],
            ['category_id' => $beer?->category_id, 'name' => 'Red Horse', 'description' => 'Red Horse Beer 500ml', 'price' => 115, 'stock_quantity' => 24, 'low_stock_threshold' => 12, 'stock_tracking' => 'manual'],
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
