<?php

namespace Database\Seeders;

use App\Models\PosCategory;
use Illuminate\Database\Seeder;

class PosCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee', 'sort_order' => 1],
            ['name' => 'Tea', 'sort_order' => 2],
            ['name' => 'Beer', 'sort_order' => 3],
            ['name' => 'Food', 'sort_order' => 4],
            ['name' => 'Dessert', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            PosCategory::firstOrCreate(
                ['name' => $category['name']],
                ['sort_order' => $category['sort_order'], 'is_active' => true]
            );
        }
    }
}
