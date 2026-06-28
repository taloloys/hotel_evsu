<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
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
        $query = PosProduct::with('category');

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
                            ->where('stock_quantity', '>', $defaultThreshold)
                            ->where('stock_quantity', '<=', (int) ($defaultThreshold * 1.4));
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('low_stock_threshold')
                            ->whereColumn('stock_quantity', '>', 'low_stock_threshold')
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
            ->paginate(20)
            ->withQueryString();

        $allProducts = PosProduct::all();

        $criticalCount = $allProducts->filter(fn ($p) => $p->stock_quantity <= $p->effectiveLowStockThreshold())->count();
        $lowCount = $allProducts->filter(fn ($p) => $p->stock_quantity > $p->effectiveLowStockThreshold() && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 1.4))->count();
        $healthyCount = $allProducts->filter(fn ($p) => $p->stock_quantity > (int) ($p->effectiveLowStockThreshold() * 1.4) && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 2))->count();
        $outOfStockCount = $allProducts->where('stock_quantity', 0)->count();

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

        return back()->with('success', 'Inventory updated successfully.');
    }
}
