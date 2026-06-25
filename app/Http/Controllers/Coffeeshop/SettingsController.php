<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosCategory;
use App\Models\PosSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('coffeeshop.settings.index', [
            'defaultLowStockThreshold' => PosSetting::defaultLowStockThreshold(),
            'walkInFolioId' => PosSetting::walkInFolioId(),
            'categories' => PosCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_low_stock_threshold' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        PosSetting::set('default_low_stock_threshold', (string) $validated['default_low_stock_threshold']);

        return back()->with('success', 'POS settings updated successfully.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:pos_categories,name'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        PosCategory::create([
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function toggleCategory(PosCategory $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        return back()->with('success', 'Category status updated.');
    }
}
