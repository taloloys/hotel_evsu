<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with RBAC summary and matrix.
     */
    public function index(): View
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::all();
        $users = User::with('role')->get();

        $totalRolesCount = $roles->count();
        $totalPermissionsCount = $permissions->count();
        $totalUsersCount = $users->count();

        $activeRoles = $roles->where('is_active', true);
        $activePermissions = $permissions->where('is_active', true);

        return view('admin.dashboard.index', compact(
            'roles',
            'permissions',
            'users',
            'totalRolesCount',
            'totalPermissionsCount',
            'totalUsersCount',
            'activeRoles',
            'activePermissions'
        ));
    }
}
