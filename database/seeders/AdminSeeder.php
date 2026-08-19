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
            'manage-users' => ['desc' => 'Manage system users and roles', 'module' => 'System'],
            'manage-shifts' => ['desc' => 'Manage shift schedules and view sales reports', 'module' => 'System'],

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

            // External Links
            'access-foodpanda' => ['desc' => 'Access Food Panda food delivery link', 'module' => 'System'],
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

        // 2. Create ADMIN role with full permissions
        $adminRole = Role::updateOrCreate(
            ['role_name' => 'ADMIN'],
            [
                'description' => 'Full system administrator with all access privileges',
                'is_system_admin' => true,
            ]
        );

        $adminRole->permissions()->sync(
            collect($permissionModels)->pluck('permission_id')->all()
        );

        // 3. Create Admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'SoftwareAdmin',
                'password_hash' => Hash::make('password'),
                'role_id' => $adminRole->role_id,
            ]
        );
    }
}
