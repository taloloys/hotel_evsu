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

        if ($request->input('filter') === 'low_stock') {
            $query->lowStock();
        }

        $products = $query->orderBy('stock_quantity')->paginate(20)->withQueryString();
        $lowStockProducts = $inventoryService->lowStockProducts();
        $defaultThreshold = PosSetting::defaultLowStockThreshold();

        return view('coffeeshop.inventory.index', compact('products', 'lowStockProducts', 'defaultThreshold'));
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
