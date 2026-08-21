<?php

use App\Models\PosApprovalRequest;
use App\Models\PosOrder;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\ChargeCodeSeeder;
use Database\Seeders\PosCategorySeeder;
use Database\Seeders\PosProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(ChargeCodeSeeder::class);
    $this->seed(PosCategorySeeder::class);
    $this->seed(PosProductSeeder::class);

    $this->admin = User::whereHas('role', fn ($q) => $q->where('role_name', 'ADMIN'))->firstOrFail();
    $this->cashier = User::whereHas('role', fn ($q) => $q->where('role_name', 'CAFETERIA'))->firstOrFail();
    $this->product = PosProduct::where('stock_tracking', 'manual')->first() ?? PosProduct::firstOrFail();
});

test('cashier cancel non-empty tab creates pending request', function (): void {
    $tab = PosTab::create([
        'tab_name' => 'Table 5',
        'tab_type' => 'walk_in',
        'status' => 'open',
        'subtotal' => 100,
        'total' => 100,
        'opened_by' => $this->cashier->user_id,
        'opened_at' => now(),
    ]);

    // Add item to make it non-empty
    $tab->items()->create([
        'product_id' => $this->product->product_id,
        'quantity' => 1,
        'unit_price' => 100,
        'line_total' => 100,
    ]);

    $response = $this->actingAs($this->cashier)
        ->postJson(route('coffeeshop.api.tabs.cancel', $tab->tab_id), [
            'reason' => 'Customer left without paying',
        ]);

    $response->assertOk();
    $response->assertJsonFragment(['message' => 'Cancellation request submitted to Admin for authorization.']);

    $this->assertDatabaseHas('pos_approval_requests', [
        'tab_id' => $tab->tab_id,
        'request_type' => 'cancel_tab',
        'status' => 'pending',
        'requested_by' => $this->cashier->user_id,
        'reason' => 'Customer left without paying',
    ]);
});

test('cashier refund closed order creates pending request', function (): void {
    // We need an active shift to close tabs/orders
    $shift = Shift::create([
        'user_id' => $this->cashier->user_id,
        'start_time' => now(),
    ]);

    $order = PosOrder::create([
        'order_number' => 'POS-20260626-0001',
        'customer_name' => 'John Walkin',
        'status' => 'closed',
        'payment_method' => 'cash',
        'subtotal' => 200,
        'total' => 200,
        'user_id' => $this->cashier->user_id,
        'shift_id' => $shift->shift_id,
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($this->cashier)
        ->post(route('coffeeshop.orders.refund', $order->order_id), [
            'reason' => 'Spilled coffee',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Refund request submitted to Admin for authorization.');

    $this->assertDatabaseHas('pos_approval_requests', [
        'order_id' => $order->order_id,
        'request_type' => 'refund',
        'status' => 'pending',
        'requested_by' => $this->cashier->user_id,
        'reason' => 'Spilled coffee',
    ]);
});

test('admin can approve pending cancel tab request', function (): void {
    $tab = PosTab::create([
        'tab_name' => 'Table 5',
        'tab_type' => 'walk_in',
        'status' => 'open',
        'subtotal' => 100,
        'total' => 100,
        'opened_by' => $this->cashier->user_id,
        'opened_at' => now(),
    ]);

    $approvalRequest = PosApprovalRequest::create([
        'tab_id' => $tab->tab_id,
        'request_type' => 'cancel_tab',
        'status' => 'pending',
        'requested_by' => $this->cashier->user_id,
        'reason' => 'Accidentally added wrong room',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.pos-approvals.approve', $approvalRequest->request_id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $approvalRequest->refresh();
    $tab->refresh();

    $this->assertEquals('approved', $approvalRequest->status);
    $this->assertEquals('cancelled', $tab->status);
    $this->assertEquals($this->admin->user_id, $approvalRequest->resolved_by);
});

test('admin can reject pending request', function (): void {
    $tab = PosTab::create([
        'tab_name' => 'Table 5',
        'tab_type' => 'walk_in',
        'status' => 'open',
        'subtotal' => 100,
        'total' => 100,
        'opened_by' => $this->cashier->user_id,
        'opened_at' => now(),
    ]);

    $approvalRequest = PosApprovalRequest::create([
        'tab_id' => $tab->tab_id,
        'request_type' => 'cancel_tab',
        'status' => 'pending',
        'requested_by' => $this->cashier->user_id,
        'reason' => 'Wrong tab',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.pos-approvals.reject', $approvalRequest->request_id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $approvalRequest->refresh();
    $tab->refresh();

    $this->assertEquals('rejected', $approvalRequest->status);
    $this->assertEquals('open', $tab->status); // unchanged
});

test('live stock update on tab operations', function (): void {
    $this->actingAs($this->cashier);

    $startingStock = $this->product->stock_quantity;

    $tabResponse = $this->postJson(route('coffeeshop.api.tabs.store'), [
        'tab_name' => 'Testing Stock',
        'tab_type' => 'walk_in',
    ])->assertCreated();

    $tabId = $tabResponse->json('tab.tab_id');

    $this->postJson(route('coffeeshop.api.tabs.items.store', $tabId), [
        'product_id' => $this->product->product_id,
        'quantity' => 2,
    ])->assertOk();

    $this->product->refresh();
    $this->assertEquals($startingStock - 2, $this->product->stock_quantity);

    $tab = PosTab::findOrFail($tabId);
    $item = $tab->items()->firstOrFail();

    $this->patchJson(route('coffeeshop.api.tabs.items.update', [$tabId, $item->tab_item_id]), [
        'quantity' => 3,
    ])->assertOk();

    $this->product->refresh();
    $this->assertEquals($startingStock - 3, $this->product->stock_quantity);

    $this->patchJson(route('coffeeshop.api.tabs.items.update', [$tabId, $item->tab_item_id]), [
        'quantity' => 1,
    ])->assertOk();

    $this->product->refresh();
    $this->assertEquals($startingStock - 1, $this->product->stock_quantity);

    $this->deleteJson(route('coffeeshop.api.tabs.items.destroy', [$tabId, $item->tab_item_id]))
        ->assertOk();

    $this->product->refresh();
    $this->assertEquals($startingStock, $this->product->stock_quantity);
});
