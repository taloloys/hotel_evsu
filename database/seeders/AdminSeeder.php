<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create system permissions
        $permissions = [
            // System Module
            'manage-users' => ['desc' => 'Manage system staff user accounts', 'module' => 'System'],
            'manage-admins' => ['desc' => 'Manage administrator accounts and privilege levels', 'module' => 'System'],
            'manage-roles-permissions' => ['desc' => 'Create and customize system roles and permissions', 'module' => 'System'],
            'manage-landing-page' => ['desc' => 'Manage landing page showcase items and room offers', 'module' => 'System'],
            'manage-backup-restore' => ['desc' => 'Access system database backup and restore tools', 'module' => 'System'],
            'manage-rooms' => ['desc' => 'Manage hotel room inventory, rates, and statuses', 'module' => 'System'],
            'manage-charge-codes' => ['desc' => 'Manage billing charge codes', 'module' => 'System'],
            'manage-credit-accounts' => ['desc' => 'Manage credit accounts and record payments', 'module' => 'System'],
            'view-activity-logs' => ['desc' => 'View and export system activity and audit logs', 'module' => 'System'],
            'manage-pos-approvals' => ['desc' => 'Approve or reject POS void and discount requests', 'module' => 'System'],
            'manage-shifts' => ['desc' => 'Manage shift schedules and view sales reports', 'module' => 'System'],
            'access-foodpanda' => ['desc' => 'Access Food Panda food delivery link', 'module' => 'System'],

            // Front Desk Module
            'manage-reservations' => ['desc' => 'Manage reservations and guest registrations', 'module' => 'Front Desk'],
            'process-checkout' => ['desc' => 'Process guest checkout and record payments', 'module' => 'Front Desk'],
            'view-guest-list' => ['desc' => 'View guest list details', 'module' => 'Front Desk'],
            'view-guest-folio' => ['desc' => 'View guest folio details', 'module' => 'Front Desk'],
            'manage-guest-folio' => ['desc' => 'Open, close, reopen folios and post charges', 'module' => 'Front Desk'],
            'view-shift-sales' => ['desc' => 'View individual or shared shift sales', 'module' => 'Front Desk'],

            // Accounting Module
            'view-accounting-dashboard' => ['desc' => 'Access financial overview charts and statistics', 'module' => 'Accounting'],
            'manage-accounting-billing' => ['desc' => 'Access billing details and view billing lists', 'module' => 'Accounting'],
            'manage-accounting-payments' => ['desc' => 'Register payments and view payment history', 'module' => 'Accounting'],
            'manage-accounting-receivables' => ['desc' => 'View receivables ledger and accounts', 'module' => 'Accounting'],
            'manage-accounting-expenses' => ['desc' => 'Track, create, and approve expenses', 'module' => 'Accounting'],
            'view-accounting-reports' => ['desc' => 'Generate system financial reports', 'module' => 'Accounting'],
            'view-accounting-audit' => ['desc' => 'Access log changes and trace operations', 'module' => 'Accounting'],

            // Inventory Module
            'manage-inventory' => ['desc' => 'Manage coffeeshop inventory and sales orders', 'module' => 'Inventory'],
        ];

        $permissionModels = [];
        foreach ($permissions as $key => $data) {
            $permissionModels[$key] = Permission::updateOrCreate(
                ['permission_key' => $key],
                [
                    'description' => $data['desc'],
                    'module' => $data['module'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Create SUPER_ADMIN role with ALL permissions (including manage-backup-restore and manage-admins)
        $superAdminRole = Role::updateOrCreate(
            ['role_name' => 'SUPER_ADMIN'],
            [
                'description' => 'Super administrator with full system owner privileges including backup/restore and admin management',
                'is_system_admin' => true,
            ]
        );

        $superAdminRole->permissions()->sync(
            collect($permissionModels)->pluck('permission_id')->all()
        );

        // 3. Create ADMIN role with standard admin permissions (excluding manage-backup-restore and manage-admins by default)
        $adminRole = Role::updateOrCreate(
            ['role_name' => 'ADMIN'],
            [
                'description' => 'Standard system administrator managing hotel operations and staff',
                'is_system_admin' => false,
            ]
        );

        $adminPermissionIds = collect($permissionModels)
            ->reject(function ($model, $key) {
                return in_array($key, ['manage-backup-restore', 'manage-admins'], true);
            })
            ->pluck('permission_id')
            ->all();

        $adminRole->permissions()->sync($adminPermissionIds);

        // 4. Create Super Admin user
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'full_name' => 'Super Administrator',
                'password_hash' => Hash::make('password'),
                'role_id' => $superAdminRole->role_id,
                'is_active' => true,
            ]
        );

        // 5. Create Standard Admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'SoftwareAdmin',
                'password_hash' => Hash::make('password'),
                'role_id' => $adminRole->role_id,
                'is_active' => true,
            ]
        );
    }
}
