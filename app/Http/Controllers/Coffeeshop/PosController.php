<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\CreditAccount;
use App\Models\PosApprovalRequest;
use App\Models\PosCategory;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosGuestChargeService;
use App\Services\Coffeeshop\PosOrderService;
use App\Services\Coffeeshop\PosTabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = PosCategory::active()->orderBy('sort_order')->get();
        $products = PosProduct::with('category')
            ->active()
            ->where(function ($q) {
                $q->where('is_stockable', false)
                    ->orWhere('stock_quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();
        $openTabs = PosTab::open()->with(['items.product', 'room', 'creditAccount'])->orderByDesc('opened_at')->get();
        $creditAccounts = CreditAccount::orderBy('account_name')->get();

        return view('coffeeshop.pos.index', compact('categories', 'products', 'openTabs', 'creditAccounts'));
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category_id');

        $products = PosProduct::with('category')->active();

        if ($categoryId && $categoryId !== 'all') {
            $products->where('category_id', $categoryId);
        }

        if ($query !== '') {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$query}%"));
            });
        } else {
            $products->where(function ($q) {
                $q->where('is_stockable', false)
                    ->orWhere('stock_quantity', '>', 0);
            });
        }

        $results = $products->orderBy('name')->limit(40)->get()->map(fn (PosProduct $product) => [
            'product_id' => $product->product_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'stock_quantity' => $product->stock_quantity,
            'is_stockable' => (bool) $product->is_stockable,
            'category' => $product->category?->name,
            'image_url' => $product->image_url,
            'is_low_stock' => $product->isLowStock(),
        ]);

        return response()->json(['products' => $results]);
    }

    public function checkedInGuests(PosGuestChargeService $chargeService): JsonResponse
    {
        return response()->json(['guests' => $chargeService->getCheckedInGuests()]);
    }

    public function storeTab(Request $request, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'tab_name' => ['required', 'string', 'max:150'],
            'tab_type' => ['nullable', 'in:walk_in,room,account'],
            'guest_id' => ['nullable', 'exists:guests,guest_id'],
            'folio_id' => ['nullable', 'exists:folios,folio_id'],
            'credit_account_id' => ['nullable', 'exists:credit_accounts,account_id'],
            'booking_id' => ['nullable', 'exists:bookings,booking_id'],
            'room_id' => ['nullable', 'exists:rooms,room_id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tab = $tabService->openTab($validated);

        return response()->json([
            'message' => 'Tab opened successfully.',
            'tab' => $tabService->formatTab($tab),
        ], 201);
    }

    public function addTabItem(Request $request, PosTab $tab, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:pos_products,product_id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        try {
            $tab = $tabService->addItem($tab, (int) $validated['product_id'], (int) ($validated['quantity'] ?? 1));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Item added to tab.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function updateTabItem(Request $request, PosTab $tab, int $item, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $tabItem = $tab->items()->where('tab_item_id', $item)->firstOrFail();

        try {
            $tab = $tabService->updateItemQuantity($tab, $tabItem, (int) $validated['quantity']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Tab updated.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function removeTabItem(PosTab $tab, int $item, PosTabService $tabService): JsonResponse
    {
        $tabItem = $tab->items()->where('tab_item_id', $item)->firstOrFail();

        try {
            $tab = $tabService->removeItem($tab, $tabItem);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Item removed.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function closeTab(Request $request, PosTab $tab, PosOrderService $orderService, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,gcash,card,room_charge,account_charge'],
            'booking_id' => ['nullable', 'exists:bookings,booking_id'],
            'folio_id' => ['nullable', 'exists:folios,folio_id'],
            'credit_account_id' => ['nullable', 'exists:credit_accounts,account_id'],
        ]);

        try {
            $order = $orderService->closeTab(
                $tab,
                $validated['payment_method'],
                isset($validated['booking_id']) ? (int) $validated['booking_id'] : null,
                isset($validated['folio_id']) ? (int) $validated['folio_id'] : null,
                isset($validated['credit_account_id']) ? (int) $validated['credit_account_id'] : null
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Tab closed and order completed.',
            'order' => $order->load('items'),
            'tab' => $tabService->formatTab($tab->fresh()),
        ]);
    }

    public function transferTab(Request $request, PosTab $tab, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'tab_type' => ['required', 'in:walk_in,room,account'],
            'folio_id' => ['required_if:tab_type,room', 'nullable', 'exists:folios,folio_id'],
            'booking_id' => ['required_if:tab_type,room', 'nullable', 'exists:bookings,booking_id'],
            'room_id' => ['required_if:tab_type,room', 'nullable', 'exists:rooms,room_id'],
            'guest_id' => ['required_if:tab_type,room', 'nullable', 'exists:guests,guest_id'],
            'credit_account_id' => ['required_if:tab_type,account', 'nullable', 'exists:credit_accounts,account_id'],
        ]);

        try {
            $tab = $tabService->transferTabBillingTarget(
                $tab,
                $validated['tab_type'],
                isset($validated['folio_id']) ? (int) $validated['folio_id'] : null,
                isset($validated['credit_account_id']) ? (int) $validated['credit_account_id'] : null,
                isset($validated['booking_id']) ? (int) $validated['booking_id'] : null,
                isset($validated['room_id']) ? (int) $validated['room_id'] : null
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Tab billing target updated.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function applyDiscount(Request $request, PosTab $tab, PosTabService $tabService): JsonResponse
    {
        $validated = $request->validate([
            'discount_type' => ['required', 'string', 'max:50'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'is_discount_percentage' => ['required', 'boolean'],
        ]);

        try {
            $tab = $tabService->applyDiscount(
                $tab,
                $validated['discount_type'],
                (float) $validated['discount_amount'],
                (bool) $validated['is_discount_percentage']
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Discount applied successfully.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function removeDiscount(PosTab $tab, PosTabService $tabService): JsonResponse
    {
        try {
            $tab = $tabService->removeDiscount($tab);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Discount removed.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function cancelTab(Request $request, PosTab $tab, PosTabService $tabService): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role?->is_system_admin || $user->role?->role_name === 'ADMIN');
        $isEmpty = $tab->items()->count() === 0;

        if ($isAdmin || $isEmpty) {
            try {
                $tab = $tabService->cancelTab($tab);
            } catch (\RuntimeException $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return response()->json([
                'message' => 'Tab cancelled.',
                'tab' => $tabService->formatTab($tab),
            ]);
        }

        $existing = PosApprovalRequest::where('tab_id', $tab->tab_id)
            ->where('request_type', 'cancel_tab')
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'A cancellation request is already pending for this tab.'], 422);
        }

        PosApprovalRequest::create([
            'tab_id' => $tab->tab_id,
            'request_type' => 'cancel_tab',
            'status' => 'pending',
            'requested_by' => $user->user_id,
            'reason' => $request->input('reason', 'Cashier requested cancellation'),
        ]);

        Cache::flush();

        return response()->json([
            'message' => 'Cancellation request submitted to Admin for authorization.',
            'tab' => $tabService->formatTab($tab),
        ]);
    }

    public function listTabs(PosTabService $tabService): JsonResponse
    {
        $tabs = PosTab::open()->with(['items.product.category', 'room'])->orderByDesc('opened_at')->get()
            ->map(fn (PosTab $tab) => $tabService->formatTab($tab));

        return response()->json(['tabs' => $tabs]);
    }
}
