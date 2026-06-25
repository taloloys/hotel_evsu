<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        $ordersQuery = PosOrder::with(['items', 'folio.guest', 'user'])
            ->orderByDesc('created_at');

        if ($status !== 'all' && $status !== 'active_tabs') {
            $ordersQuery->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        $orders = $status === 'active_tabs'
            ? collect()
            : $ordersQuery->paginate(20)->withQueryString();

        $activeTabs = PosTab::open()
            ->with(['items.product', 'room'])
            ->when($request->filled('search'), fn ($q) => $q->where('tab_name', 'like', '%'.$request->search.'%'))
            ->orderByDesc('opened_at')
            ->paginate(20, ['*'], 'tabs_page')
            ->withQueryString();

        return view('coffeeshop.orders.index', compact('orders', 'activeTabs', 'status'));
    }

    public function show(PosOrder $order): View
    {
        $order->load(['items.product', 'folio.guest', 'transaction', 'user', 'tab']);

        return view('coffeeshop.orders.show', compact('order'));
    }

    public function refund(PosOrder $order, PosOrderService $orderService): RedirectResponse
    {
        try {
            $orderService->refundOrder($order);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }

        return back()->with('success', 'Order refunded and inventory restored.');
    }

    public function cancel(PosOrder $order, PosOrderService $orderService): RedirectResponse
    {
        try {
            $orderService->cancelOrder($order);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }

        return back()->with('success', 'Order cancelled.');
    }
}
