<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserAccountCreatedMail;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $usersQuery = User::with(['role', 'permissions']);
        if (! $currentUser?->isSuperAdmin()) {
            $usersQuery->whereHas('role', function ($q) {
                $q->where('role_name', '!=', 'SUPER_ADMIN')
                    ->where('is_system_admin', false);
            });
        }
        $users = $usersQuery->get();

        $rolesQuery = Role::where('is_active', true);
        if (! $currentUser?->isSuperAdmin()) {
            $rolesQuery->whereNotIn('role_name', ['SUPER_ADMIN', 'ADMIN']);
        }
        $roles = $rolesQuery->get();

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
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
            'password' => ['required', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,permission_id'],
        ]);

        $targetRole = Role::find($validated['role_id']);
        if ($targetRole && in_array($targetRole->role_name, ['SUPER_ADMIN', 'ADMIN'], true) && ! $currentUser?->isSuperAdmin()) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['role_id' => 'Only Super Administrators can create or assign Admin accounts.']);
        }

        $user = User::create([
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => true,
        ]);

        $user->permissions()->sync($request->input('permissions', []));

        if (! empty($user->email)) {
            try {
                Mail::to($user->email)->queue(new UserAccountCreatedMail($user, $validated['password']));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send account creation email: ' . $e->getMessage());
            }
        }

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
        /** @var User $currentUser */
        $currentUser = auth()->user();

        if ($currentUser->user_id === $user->user_id) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['cannot_disable_self' => 'You cannot disable your own administrator account.']);
        }

        if ($user->isSuperAdmin() && ! $currentUser->isSuperAdmin()) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['unauthorized' => 'You do not have permission to modify Super Administrator accounts.']);
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
        /** @var User $currentUser */
        $currentUser = auth()->user();

        if ($user->isSuperAdmin() && ! $currentUser->isSuperAdmin()) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['unauthorized' => 'You do not have permission to modify Super Administrator accounts.']);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$user->user_id.',user_id'],
            'email' => ['nullable', 'email', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
            'password' => ['nullable', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,permission_id'],
        ]);

        $targetRole = Role::find($validated['role_id']);
        if ($targetRole && in_array($targetRole->role_name, ['SUPER_ADMIN', 'ADMIN'], true) && ! $currentUser?->isSuperAdmin() && $user->role_id !== (int) $validated['role_id']) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['role_id' => 'Only Super Administrators can assign Admin roles.']);
        }

        $updateData = [
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
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

    /**
     * Reset the password for a specified user.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        if ($user->isSuperAdmin() && ! $currentUser->isSuperAdmin()) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['unauthorized' => 'You do not have permission to reset Super Administrator passwords.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        ActivityLog::log(
            'USER_PASSWORD_RESET',
            "Reset password for user account {$user->full_name} (username: {$user->username})."
        );

        return redirect()
            ->route('admin.users')
            ->with('success', "Password for user {$user->username} has been reset successfully.");
    }
}
