<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PosProduct;
use App\Models\PosSetting;
use App\Services\Coffeeshop\PosInventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request, PosInventoryService $inventoryService): View
    {
        // Only show manually-tracked products on the Inventory page.
        // Made-to-order (none) products never appear here.
        $query = PosProduct::with('category')->where('stock_tracking', 'manual');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $filter = $request->input('filter');
        $defaultThreshold = PosSetting::defaultLowStockThreshold();

        switch ($filter) {

            case 'critical_stock':
                $query->where(function ($q) use ($defaultThreshold) {
                    $q->where(function ($sub) use ($defaultThreshold) {
                        $sub->whereNull('low_stock_threshold')
                            ->where('stock_quantity', '<=', $defaultThreshold);
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('low_stock_threshold')
                            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    });
                });
                break;

            case 'low_stock':
                $query->where(function ($q) use ($defaultThreshold) {
                    $q->where(function ($sub) use ($defaultThreshold) {
                        $sub->whereNull('low_stock_threshold')
                            ->where('stock_quantity', '<=', (int) ($defaultThreshold * 1.4));
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('low_stock_threshold')
                            ->whereRaw('stock_quantity <= (low_stock_threshold * 1.4)');
                    });
                });
                break;

            case 'healthy_stock':
                $query->where(function ($q) use ($defaultThreshold) {
                    $q->where(function ($sub) use ($defaultThreshold) {
                        $sub->whereNull('low_stock_threshold')
                            ->where('stock_quantity', '>', (int) ($defaultThreshold * 1.4))
                            ->where('stock_quantity', '<=', (int) ($defaultThreshold * 2));
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('low_stock_threshold')
                            ->whereRaw('stock_quantity > (low_stock_threshold * 1.4)')
                            ->whereRaw('stock_quantity <= (low_stock_threshold * 2)');
                    });
                });
                break;

            case 'out_of_stock':
                $query->where('stock_quantity', 0);
                break;

            case 'well_stocked':
                $query->where(function ($q) use ($defaultThreshold) {
                    $q->where(function ($sub) use ($defaultThreshold) {
                        $sub->whereNull('low_stock_threshold')
                            ->where('stock_quantity', '>', (int) ($defaultThreshold * 2));
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('low_stock_threshold')
                            ->whereRaw('stock_quantity > (low_stock_threshold * 2)');
                    });
                });
                break;
        }

        $products = $query->orderBy('stock_quantity', 'asc')
            ->paginate(10)
            ->withQueryString();

        // All summary counts are scoped to manual-tracked products only.
        $allManualProducts = PosProduct::where('stock_tracking', 'manual')->get();

        $criticalCount = $allManualProducts->filter(fn ($p) => $p->stock_quantity <= $p->effectiveLowStockThreshold())->count();
        $lowCount = $allManualProducts->filter(fn ($p) => $p->stock_quantity > $p->effectiveLowStockThreshold() && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 1.4))->count();
        $healthyCount = $allManualProducts->filter(fn ($p) => $p->stock_quantity > (int) ($p->effectiveLowStockThreshold() * 1.4) && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 2))->count();
        $outOfStockCount = $allManualProducts->where('stock_quantity', 0)->count();

        $lowStockProducts = $inventoryService->lowStockProducts();

        return view('coffeeshop.inventory.index', compact(
            'products',
            'lowStockProducts',
            'defaultThreshold',
            'criticalCount',
            'lowCount',
            'healthyCount',
            'outOfStockCount'
        ));
    }

    public function adjust(Request $request, PosProduct $product, PosInventoryService $inventoryService): RedirectResponse
    {
        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:restock,adjustment'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $originalStock = $product->stock_quantity;

        try {
            $inventoryService->adjustStock(
                $product,
                (int) $validated['quantity'],
                $validated['adjustment_type'],
                'manual',
                null,
                $validated['notes'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }

        $product->refresh();
        $newStock = $product->stock_quantity;

        $actionType = $validated['adjustment_type'] === 'restock' ? 'RESTOCK_PRODUCT' : 'ADJUST_PRODUCT';
        $logMsg = ucfirst($validated['adjustment_type'])."ed product \"{$product->name}\": quantity adjusted by {$validated['quantity']} units (Stock: {$originalStock} -> {$newStock}).";
        if (! empty($validated['notes'])) {
            $logMsg .= " Notes: {$validated['notes']}";
        }

        ActivityLog::log($actionType, $logMsg);

        return redirect()
            ->route('coffeeshop.inventory')
            ->with('success', "Inventory updated for \"{$product->name}\". New stock: {$newStock}.");
    }
}
