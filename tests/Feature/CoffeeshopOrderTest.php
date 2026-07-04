<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoffeeshopOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_page_does_not_show_active_tabs_filter(): void
    {
        $this->seed(UserSeeder::class);

        $user = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();

        $this->actingAs($user)
            ->get(route('coffeeshop.orders'))
            ->assertOk()
            ->assertSee('All Orders')
            ->assertDontSee('Active Tabs')
            ->assertDontSee('No active tabs');
    }
}
