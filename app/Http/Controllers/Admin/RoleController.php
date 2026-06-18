<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create([
            'role_name' => strtoupper($validated['role_name']),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.roles')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name,'.$role->role_id.',role_id'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update([
            'role_name' => strtoupper($validated['role_name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.roles')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Toggle the active status of a role.
     */
    public function toggleStatus(Role $role): RedirectResponse
    {
        // Prevent disabling the ADMIN role
        if (strtoupper($role->role_name) === 'ADMIN') {
            return redirect()
                ->route('admin.roles')
                ->withErrors(['cannot_disable_admin' => 'The Administrator role cannot be disabled.']);
        }

        $role->update(['is_active' => ! $role->is_active]);

        $statusMessage = $role->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.roles')
            ->with('success', "Role has been successfully {$statusMessage}.");
    }
}
