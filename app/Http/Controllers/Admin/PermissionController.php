<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display a listing of all permissions.
     */
    public function index(): View
    {
        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('permission_key')
            ->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'permission_key' => ['required', 'string', 'max:100', 'unique:permissions,permission_key', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'description' => ['required', 'string', 'max:255'],
            'module' => ['required', 'string', 'in:System,Front Desk,Accounting,Inventory,POS'],
        ], [
            'permission_key.regex' => 'The permission key must only contain letters, numbers, dashes, and underscores.',
        ]);

        Permission::create([
            'permission_key' => strtolower($validated['permission_key']),
            'description' => $validated['description'],
            'module' => $validated['module'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.permissions')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'permission_key' => ['required', 'string', 'max:100', 'unique:permissions,permission_key,'.$permission->permission_id.',permission_id', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'description' => ['required', 'string', 'max:255'],
            'module' => ['required', 'string', 'in:System,Front Desk,Accounting,Inventory,POS'],
        ], [
            'permission_key.regex' => 'The permission key must only contain letters, numbers, dashes, and underscores.',
        ]);

        $permission->update([
            'permission_key' => strtolower($validated['permission_key']),
            'description' => $validated['description'],
            'module' => $validated['module'],
        ]);

        return redirect()
            ->route('admin.permissions')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Toggle the active status of a permission.
     */
    public function toggleStatus(Permission $permission): RedirectResponse
    {
        // Prevent disabling key system permissions
        if ($permission->permission_key === 'manage-users') {
            return redirect()
                ->route('admin.permissions')
                ->withErrors(['cannot_disable_manage_users' => 'The "manage-users" permission is a critical system permission and cannot be disabled.']);
        }

        $permission->update(['is_active' => ! $permission->is_active]);

        $statusMessage = $permission->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.permissions')
            ->with('success', "Permission has been successfully {$statusMessage}.");
    }
}
