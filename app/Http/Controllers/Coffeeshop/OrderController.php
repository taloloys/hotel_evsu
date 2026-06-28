<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosApprovalRequest;
use App\Models\PosOrder;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function statusJson(PosOrder $order): JsonResponse
    {
        $pendingRefund = PosApprovalRequest::where('order_id', $order->order_id)
            ->where('request_type', 'refund')
            ->where('status', 'pending')
            ->first();

        $pendingCancel = PosApprovalRequest::where('order_id', $order->order_id)
            ->where('request_type', 'cancel_order')
            ->where('status', 'pending')
            ->first();

        return response()->json([
            'order_id' => $order->order_id,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'pending_refund' => $pendingRefund ? [
                'request_id' => $pendingRefund->request_id,
                'reason' => $pendingRefund->reason,
            ] : null,
            'pending_cancel' => $pendingCancel ? [
                'request_id' => $pendingCancel->request_id,
                'reason' => $pendingCancel->reason,
            ] : null,
        ]);
    }

    public function refund(Request $request, PosOrder $order, PosOrderService $orderService): RedirectResponse
    {
        $user = auth()->user();

        $isAdmin = $user && (
            $user->role?->is_system_admin ||
            $user->role?->role_name === 'ADMIN'
        );

        // 🚨 1. HARD STOP: already refunded
        if ($order->status === 'refunded') {
            return back()->withErrors([
                'order' => 'This order has already been refunded.'
            ]);
        }

        // 🚨 2. HARD STOP: already has pending request
        $existing = PosApprovalRequest::where('order_id', $order->order_id)
            ->where('request_type', 'refund')
            ->where('status', 'pending')
            ->exists();

        if ($existing && !$isAdmin) {
            return back()->withErrors([
                'order' => 'A refund request is already pending for this order.'
            ]);
        }

        if ($isAdmin) {
            try {
                $orderService->refundOrder($order);

                // 🔒 extra safety: ensure status is locked immediately
                $order->refresh();

                if ($order->status === 'refunded') {
                    return back()->with('success', 'Order already refunded.');
                }

            } catch (\RuntimeException $e) {
                return back()->withErrors(['order' => $e->getMessage()]);
            }

            return back()->with('success', 'Order refunded and inventory restored.');
        }

        // 🚨 prevent duplicate request creation
        PosApprovalRequest::create([
            'order_id' => $order->order_id,
            'request_type' => 'refund',
            'status' => 'pending',
            'requested_by' => $user->user_id,
            'reason' => $request->input('reason', 'Cashier requested refund'),
        ]);

        Cache::flush();

        return back()->with('success', 'Refund request submitted to Admin for authorization.');
    }

    public function cancel(Request $request, PosOrder $order, PosOrderService $orderService): RedirectResponse
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role?->is_system_admin || $user->role?->role_name === 'ADMIN');

        if ($isAdmin) {
            try {
                $orderService->cancelOrder($order);
            } catch (\RuntimeException $e) {
                return back()->withErrors(['order' => $e->getMessage()]);
            }

            return back()->with('success', 'Order cancelled.');
        }

        $existing = PosApprovalRequest::where('order_id', $order->order_id)
            ->where('request_type', 'cancel_order')
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->withErrors(['order' => 'A cancellation request is already pending for this order.']);
        }

        PosApprovalRequest::create([
            'order_id' => $order->order_id,
            'request_type' => 'cancel_order',
            'status' => 'pending',
            'requested_by' => $user->user_id,
            'reason' => $request->input('reason', 'Cashier requested cancellation'),
        ]);

        Cache::flush();

        return back()->with('success', 'Cancellation request submitted to Admin for authorization.');
    }
}
