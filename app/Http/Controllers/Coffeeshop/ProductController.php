<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PosCategory;
use App\Models\PosProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = PosCategory::orderBy('sort_order')->get();

        return view('coffeeshop.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = PosCategory::active()->orderBy('sort_order')->get();

        return view('coffeeshop.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedProduct($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('pos/products', 'public');
        }

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

    public function update(Request $request, PosProduct $product): RedirectResponse
    {
        $validated = $this->validatedProduct($request, $product->product_id);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('pos/products', 'public');
        }

        $product->update($validated);

        ActivityLog::log(
            'EDIT_PRODUCT',
            "Updated coffeeshop product: {$product->name}"
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

    private function validatedProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:pos_categories,category_id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
