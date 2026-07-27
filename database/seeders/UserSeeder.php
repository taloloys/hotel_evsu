<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create permissions
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

        // 2. Create roles
        $roles = [
            'ADMIN' => [
                'label' => 'Administrator',
                'description' => 'Full system administrator with all access privileges',
                'permissions' => [
                    'manage-users',
                    'manage-shifts',
                    'manage-reservations',
                    'process-checkout',
                    'view-guest-list',
                    'view-guest-folio',
                    'manage-guest-folio',
                    'view-shift-sales',
                    'view-accounting-dashboard',
                    'manage-accounting-billing',
                    'manage-accounting-payments',
                    'manage-accounting-receivables',
                    'manage-accounting-expenses',
                    'view-accounting-reports',
                    'view-accounting-audit',
                    'manage-inventory',
                    'access-foodpanda',
                ],
            ],
            'FRONT_DESK' => [
                'label' => 'Front Desk Operations',
                'description' => 'Front desk receptionist handling bookings, check-ins, and folios',
                'permissions' => [
                    'manage-reservations',
                    'process-checkout',
                    'view-guest-list',
                    'view-guest-folio',
                    'manage-guest-folio',
                    'view-shift-sales',
                ],
            ],
            'ACCOUNTING' => [
                'label' => 'Accounting & Finance',
                'description' => 'Finance staff auditing invoices, payments, and sales reports',
                'permissions' => [
                    'view-accounting-dashboard',
                    'manage-accounting-billing',
                    'manage-accounting-payments',
                    'manage-accounting-receivables',
                    'manage-accounting-expenses',
                    'view-accounting-reports',
                    'view-accounting-audit',
                ],
            ],
            'CAFETERIA' => [
                'label' => 'Cafeteria / POS',
                'description' => 'Cafeteria cashier managing orders, POS, and inventory',
                'permissions' => ['manage-inventory'],
            ],
        ];

        foreach ($roles as $roleName => $data) {
            $role = Role::updateOrCreate(
                ['role_name' => $roleName],
                [
                    'description' => $data['description'],
                    'is_system_admin' => $roleName === 'ADMIN',
                ]
            );

            // Sync permissions for this role
            $rolePermissionIds = [];
            foreach ($data['permissions'] as $permKey) {
                if (isset($permissionModels[$permKey])) {
                    $rolePermissionIds[] = $permissionModels[$permKey]->permission_id;
                }
            }
            $role->permissions()->sync($rolePermissionIds);
        }

        // 3. Create users and link to roles
        $users = [
            [
                'username' => 'admin',
                'full_name' => 'SoftwareAdmin',
                'password_hash' => Hash::make('password'),
                'role_name' => 'ADMIN',
            ],
            [
                'username' => 'frontdesk',
                'full_name' => 'Front Desk User',
                'password_hash' => Hash::make('password'),
                'role_name' => 'FRONT_DESK',
            ],
            [
                'username' => 'accounting',
                'full_name' => 'Cashier',
                'password_hash' => Hash::make('password'),
                'role_name' => 'ACCOUNTING',
            ],
            [
                'username' => 'cafeteria',
                'full_name' => 'Cafeteria User',
                'password_hash' => Hash::make('password'),
                'role_name' => 'CAFETERIA',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('role_name', $userData['role_name'])->first();

            User::updateOrCreate(
                ['username' => $userData['username']],
                [
                    'full_name' => $userData['full_name'],
                    'password_hash' => $userData['password_hash'],
                    'role_id' => $role->role_id,
                ]
            );
        }
    }
}
