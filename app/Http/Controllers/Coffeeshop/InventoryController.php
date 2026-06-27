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

        switch ($filter) {

            case 'critical_stock':
                $query->where('stock_quantity', '<', 50);
                break;

            case 'low_stock':
                $query->whereBetween('stock_quantity', [50, 69]);
                break;

            case 'healthy_stock':
                $query->whereBetween('stock_quantity', [70, 100]);
                break;

            case 'out_of_stock':
                $query->where('stock_quantity', 0);
                break;

            case 'well_stocked':
                $query->where('stock_quantity', '>', 100);
                break;
        }

        $products = $query->orderBy('stock_quantity', 'asc')
            ->paginate(20)
            ->withQueryString();

        $allProducts = PosProduct::all();

        $criticalCount = $allProducts->where('stock_quantity', '<', 50)->count();
        $lowCount = $allProducts->whereBetween('stock_quantity', [50, 69])->count();
        $healthyCount = $allProducts->whereBetween('stock_quantity', [70, 100])->count();

        $lowStockProducts = $inventoryService->lowStockProducts();
        $defaultThreshold = PosSetting::defaultLowStockThreshold();

        return view('coffeeshop.inventory.index', compact(
            'products',
            'lowStockProducts',
            'defaultThreshold',
            'criticalCount',
            'lowCount',
            'healthyCount'
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
