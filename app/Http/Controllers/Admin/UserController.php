<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::with(['role', 'permissions'])->get();
        $roles = Role::where('is_active', true)->get();
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('permission_key')
            ->get();

        $totalCount = $users->count();
        $activeCount = $users->where('is_active', true)->count();
        $inactiveCount = $users->where('is_active', false)->count();

        return view('admin.users.index', compact('users', 'roles', 'permissions', 'totalCount', 'activeCount', 'inactiveCount'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
            'password' => ['required', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,permission_id'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => true,
        ]);

        $user->permissions()->sync($request->input('permissions', []));

        ActivityLog::log(
            'USER_CREATED',
            "Created user account for {$validated['full_name']} (username: {$validated['username']})."
        );

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if (auth()->id() === $user->user_id) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['cannot_disable_self' => 'You cannot disable your own administrator account.']);
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        ActivityLog::log(
            'USER_STATUS_TOGGLED',
            "Toggled user account status for {$user->full_name} (username: {$user->username}) to ".($user->is_active ? 'ENABLED' : 'DISABLED').'.'
        );

        $statusMessage = $user->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.users')
            ->with('success', "User account has been successfully {$statusMessage}.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$user->user_id.',user_id'],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
            'password' => ['nullable', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,permission_id'],
        ]);

        $updateData = [
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'role_id' => $validated['role_id'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password_hash'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $user->permissions()->sync($request->input('permissions', []));
        $user->refresh(); // Clear the resolvedPermissions cache

        ActivityLog::log(
            'USER_UPDATED',
            "Updated user account details for {$user->full_name} (username: {$user->username})."
        );

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }
}
