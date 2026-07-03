<?php

use App\Models\PosOrder;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\PosProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('exported report respects selected payment method filter', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(PosCategorySeeder::class);
    $this->seed(PosProductSeeder::class);

    $user = User::where('username', 'cafeteria')->firstOrFail();
    $shift = Shift::create([
        'user_id' => $user->user_id,
        'start_time' => now(),
    ]);

    $closedAt = now()->subDay()->setTime(13, 30, 0);

    PosOrder::create([
        'order_number' => 'POS-001',
        'customer_name' => 'Walk-in Customer',
        'status' => 'closed',
        'payment_method' => 'cash',
        'subtotal' => 100.00,
        'total' => 100.00,
        'user_id' => $user->user_id,
        'shift_id' => $shift->shift_id,
        'closed_at' => $closedAt,
    ]);

    PosOrder::create([
        'order_number' => 'POS-002',
        'customer_name' => 'Room Charge Customer',
        'status' => 'closed',
        'payment_method' => 'room_charge',
        'subtotal' => 200.00,
        'total' => 200.00,
        'user_id' => $user->user_id,
        'shift_id' => $shift->shift_id,
        'closed_at' => $closedAt,
    ]);

    $dateFrom = now()->subDays(7)->toDateString();
    $dateTo = now()->toDateString();

    $response = $this->actingAs($user)
        ->get(route('coffeeshop.reports.export', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'payment_method' => 'cash',
        ]));

    $response->assertOk();
    $response->assertHeaderContains('content-type', 'text/csv');
    $response->assertDownload();
    $response->assertStreamed();
    $content = $response->streamedContent();
    $this->assertStringContainsString('POS-001', $content);
    $this->assertStringNotContainsString('POS-002', $content);
    $this->assertStringContainsString($closedAt->format('Y-m-d H:i:s'), $content);
});
