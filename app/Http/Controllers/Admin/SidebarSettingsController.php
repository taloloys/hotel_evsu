<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\UserModulePreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SidebarSettingsController extends Controller
{
    /**
     * List of configurable modules for sidebar visibility.
     */
    protected array $configurableModules = [
        'frontdesk' => [
            'name' => 'Front Desk',
            'icon' => 'fa-concierge-bell',
            'description' => 'Reservations, Guest Registrations, Guest Folios, and Shift Sales.',
            'category' => 'Operations',
        ],
        'coffeeshop' => [
            'name' => 'Cafeteria / Coffee Shop',
            'icon' => 'fa-mug-hot',
            'description' => 'Point of Sale (POS), Products, Inventory tracking, Tabs, and POS Reports.',
            'category' => 'Retail & POS',
        ],
        'accounting' => [
            'name' => 'Accounting & Finance',
            'icon' => 'fa-calculator',
            'description' => 'Billing, Payments, Receivables, Expense approvals, and Audit logs.',
            'category' => 'Finance',
        ],
        'food_delivery' => [
            'name' => 'Food Delivery',
            'icon' => 'fa-utensils',
            'description' => 'External food delivery ordering system integration.',
            'category' => 'Services',
        ],
    ];

    /**
     * Display Super Admin's personal sidebar visibility settings.
     */
    public function index(): View
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to Sidebar Settings.');
        }

        $preferences = UserModulePreference::where('user_id', $user->user_id)
            ->pluck('is_visible', 'module_key')
            ->toArray();

        $modules = [];
        foreach ($this->configurableModules as $key => $meta) {
            $modules[] = [
                'key' => $key,
                'name' => $meta['name'],
                'icon' => $meta['icon'],
                'description' => $meta['description'],
                'category' => $meta['category'],
                'is_visible' => (bool) ($preferences[$key] ?? true),
            ];
        }

        $visibleCount = count(array_filter($modules, fn ($m) => $m['is_visible']));
        $hiddenCount = count($modules) - $visibleCount;

        return view('admin.sidebar-settings.index', compact('modules', 'visibleCount', 'hiddenCount'));
    }

    /**
     * Toggle personal sidebar visibility for a specific module.
     */
    public function toggle(Request $request, string $moduleKey): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to Sidebar Settings.');
        }

        if (! array_key_exists($moduleKey, $this->configurableModules)) {
            return redirect()
                ->route('admin.sidebar-settings')
                ->withErrors(['invalid_module' => 'Invalid module key selected.']);
        }

        $pref = UserModulePreference::where('user_id', $user->user_id)
            ->where('module_key', $moduleKey)
            ->first();

        $newStatus = $pref ? ! $pref->is_visible : false;

        UserModulePreference::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'module_key' => $moduleKey,
            ],
            [
                'is_visible' => $newStatus,
            ]
        );

        $moduleName = $this->configurableModules[$moduleKey]['name'];
        $statusLabel = $newStatus ? 'shown in' : 'hidden from';

        ActivityLog::log(
            'SIDEBAR_PREFERENCE_UPDATED',
            "Updated personal sidebar preference: '{$moduleName}' is now {$statusLabel} your sidebar navigation."
        );

        return redirect()
            ->route('admin.sidebar-settings')
            ->with('success', "'{$moduleName}' is now {$statusLabel} your sidebar navigation.");
    }
}
