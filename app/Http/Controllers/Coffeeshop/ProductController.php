<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PosCategory;
use App\Models\PosProduct;
use App\Models\PosSetting;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = PosProduct::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter') && $request->filter === 'low_stock') {
            $defaultThreshold = PosSetting::defaultLowStockThreshold();
            $query->where(function ($q) use ($defaultThreshold) {
                $q->where(function ($sub) use ($defaultThreshold) {
                    $sub->whereNull('low_stock_threshold')
                        ->where('stock_quantity', '<=', (int) ($defaultThreshold * 1.4));
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('low_stock_threshold')
                        ->whereRaw('stock_quantity <= (low_stock_threshold * 1.4)');
                });
            });
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = PosCategory::orderBy('sort_order')->get();

        return view('coffeeshop.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = PosCategory::active()->orderBy('sort_order')->get();

        return view('coffeeshop.products.create', compact('categories'));
    }

    public function store(Request $request, ImageUploadService $imageUploadService): RedirectResponse
    {
        $validated = $this->validatedProduct($request);

        $validated['image_path'] = $imageUploadService->handleUpload($request, 'image', 'pos/products');

        $product = PosProduct::create($validated);

        ActivityLog::log(
            'ADD_PRODUCT',
            "Created new coffeeshop product: {$product->name} (₱{$product->price})"
        );

        return redirect()->route('coffeeshop.products')->with('success', 'Product created successfully.');
    }

    public function edit(PosProduct $product): View
    {
        $categories = PosCategory::active()->orderBy('sort_order')->get();

        return view('coffeeshop.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, PosProduct $product, ImageUploadService $imageUploadService): RedirectResponse
    {
        $validated = $this->validatedProduct($request, $product->product_id);

        $validated['image_path'] = $imageUploadService->handleUpload(
            $request,
            'image',
            'pos/products',
            $product->image_path
        );

        $oldName = $product->name;
        $product->update($validated);
        $changes = $product->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            $changeDesc = 'No fields changed.';
        } else {
            $parts = [];
            foreach ($changes as $key => $val) {
                if ($key === 'category_id') {
                    $oldCat = PosCategory::find($product->getOriginal('category_id'))?->name ?? 'None';
                    $newCat = PosCategory::find($val)?->name ?? 'None';
                    $parts[] = "category changed from '{$oldCat}' to '{$newCat}'";
                } elseif ($key === 'is_active') {
                    $oldStatus = $product->getOriginal('is_active') ? 'Active' : 'Inactive';
                    $newStatus = $val ? 'Active' : 'Inactive';
                    $parts[] = "status changed from '{$oldStatus}' to '{$newStatus}'";
                } elseif ($key === 'stock_tracking') {
                    $oldTracking = $product->getOriginal('stock_tracking');
                    $parts[] = "stock tracking changed from '{$oldTracking}' to '{$val}'";
                } else {
                    $oldVal = $product->getOriginal($key);
                    $parts[] = "{$key} changed from '{$oldVal}' to '{$val}'";
                }
            }
            $changeDesc = implode(', ', $parts);
        }

        ActivityLog::log(
            'EDIT_PRODUCT',
            "Updated coffeeshop product: {$oldName}. Changes: {$changeDesc}."
        );

        return redirect()->route('coffeeshop.products')->with('success', 'Product updated successfully.');
    }

    public function destroy(PosProduct $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        ActivityLog::log(
            'DEACTIVATE_PRODUCT',
            "Deactivated coffeeshop product: {$product->name}"
        );

        return back()->with('success', 'Product deactivated successfully.');
    }

    public function toggleActive(PosProduct $product): RedirectResponse
    {
        $newStatus = ! $product->is_active;
        $product->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'Activated' : 'Deactivated';

        ActivityLog::log(
            strtoupper($statusText).'_PRODUCT',
            "{$statusText} coffeeshop product: {$product->name}"
        );

        return back()->with('success', "Product {$statusText} successfully.");
    }

    private function validatedProduct(Request $request, ?int $productId = null): array
    {
        $isManual = $request->input('stock_tracking') === 'manual';

        $validated = $request->validate([
            'category_id' => ['required', 'exists:pos_categories,category_id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => [$isManual ? 'required' : 'nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'stock_tracking' => ['nullable', 'in:manual,none'],
            // Image validation is now handled in ImageUploadService
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['stock_tracking'] = $request->input('stock_tracking', 'manual');

        if ($validated['stock_tracking'] !== 'manual') {
            $validated['stock_quantity'] = 0;
            $validated['low_stock_threshold'] = null;
        }

        return $validated;
    }
}
