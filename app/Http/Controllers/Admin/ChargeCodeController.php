<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChargeCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChargeCodeController extends Controller
{
    /**
     * Display a listing of the charge codes with search, filter, and statistics.
     */
    public function index(Request $request): View
    {
        $query = ChargeCode::query();

        // Search by charge code or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('charge_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by status (active/inactive)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $chargeCodes = $query->orderBy('charge_code')->paginate(20)->withQueryString();

        // Compute statistics
        $allChargeCodes = ChargeCode::all();
        $totalCount = $allChargeCodes->count();
        $activeCount = $allChargeCodes->where('is_active', true)->count();
        $inactiveCount = $allChargeCodes->where('is_active', false)->count();

        // Standard categories supported by the enum in database schema
        $categories = ['HOTEL', 'RESTAURANT', 'TAX_SERVICE', 'PAYMENT'];

        return view('admin.chargecodes.index', [
            'chargeCodes' => $chargeCodes,
            'categories' => $categories,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category ?? 'all',
                'status' => $request->status ?? 'all',
            ],
        ]);
    }

    /**
     * Store a newly created charge code in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'charge_code' => ['required', 'integer', 'min:1', 'unique:chargecodes,charge_code'],
            'description' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:HOTEL,RESTAURANT,TAX_SERVICE,PAYMENT'],
            'slug' => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', 'unique:chargecodes,slug'],
        ]);

        ChargeCode::create([
            'charge_code' => $validated['charge_code'],
            'slug' => $validated['slug'] ?? null,
            'description' => strtoupper($validated['description']),
            'category' => $validated['category'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.chargecodes')
            ->with('success', 'Charge code created successfully.');
    }

    /**
     * Update the specified charge code in storage.
     */
    public function update(Request $request, ChargeCode $chargeCode): RedirectResponse
    {
        $rules = [
            'description' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:HOTEL,RESTAURANT,TAX_SERVICE,PAYMENT'],
        ];

        // Only Super Admin can change the slug
        $isSuperAdmin = auth()->user()?->isSuperAdmin();
        if ($isSuperAdmin) {
            $rules['slug'] = ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', 'unique:chargecodes,slug,'.$chargeCode->charge_code.',charge_code'];
        }

        $validated = $request->validate($rules);

        $data = [
            'description' => strtoupper($validated['description']),
            'category' => $validated['category'],
        ];

        if ($isSuperAdmin && array_key_exists('slug', $validated)) {
            $data['slug'] = $validated['slug'] ?: null;
        }

        $chargeCode->update($data);

        return redirect()
            ->route('admin.chargecodes')
            ->with('success', 'Charge code updated successfully.');
    }

    /**
     * Toggle the active status of a charge code.
     */
    public function toggleStatus(ChargeCode $chargeCode): RedirectResponse
    {
        // Prevent disabling ROOM CHARGE, GOVERNMENT TAX, or SERVICE CHARGE if it might break basic check-in workflow
        // But for maximum flexibility, we can just allow toggling everything, or check if it is active.
        $chargeCode->update([
            'is_active' => ! $chargeCode->is_active,
        ]);

        $statusMessage = $chargeCode->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.chargecodes')
            ->with('success', "Charge code [{$chargeCode->charge_code}] has been successfully {$statusMessage}.");
    }
}
