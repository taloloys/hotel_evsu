<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $rolesQuery = Role::with('permissions')->withCount('users');
        if (! $currentUser?->isSuperAdmin()) {
            $rolesQuery->where('role_name', '!=', 'SUPER_ADMIN')
                ->where('is_system_admin', false);
        }
        $roles = $rolesQuery->get();

        $permissions = Permission::where('is_active', true)->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,permission_id'],
        ]);

        $role = Role::create([
            'role_name' => strtoupper($validated['role_name']),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()
            ->route('admin.roles')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // Prevent editing the ADMIN or SUPER_ADMIN role
        if (in_array(strtoupper($role->role_name), ['ADMIN', 'SUPER_ADMIN'], true)) {
            return redirect()
                ->route('admin.roles')
                ->withErrors(['cannot_edit_admin' => 'System administrator roles cannot be modified.']);
        }

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name,'.$role->role_id.',role_id'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,permission_id'],
        ]);

        $role->update([
            'role_name' => strtoupper($validated['role_name']),
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Toggle the active status of a role.
     */
    public function toggleStatus(Role $role): RedirectResponse
    {
        // Prevent disabling the ADMIN or SUPER_ADMIN role
        if (in_array(strtoupper($role->role_name), ['ADMIN', 'SUPER_ADMIN'], true)) {
            return redirect()
                ->route('admin.roles')
                ->withErrors(['cannot_disable_admin' => 'System administrator roles cannot be disabled.']);
        }

        $role->update(['is_active' => ! $role->is_active]);

        $statusMessage = $role->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.roles')
            ->with('success', "Role has been successfully {$statusMessage}.");
    }
}
