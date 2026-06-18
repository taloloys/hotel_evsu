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
            'manage-users' => ['desc' => 'Manage system users and roles', 'module' => 'System'],
            'manage-reservations' => ['desc' => 'Manage reservations and guest registrations', 'module' => 'Front Desk'],
            'view-folio' => ['desc' => 'View guest folio and billing status', 'module' => 'Accounting'],
            'process-checkout' => ['desc' => 'Process guest checkout and record payments', 'module' => 'Front Desk'],
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

        // 2. Create roles
        $roles = [
            'ADMIN' => [
                'label' => 'Administrator',
                'description' => 'Full system administrator with all access privileges',
                'permissions' => ['manage-users', 'manage-reservations', 'view-folio', 'process-checkout', 'manage-inventory'],
            ],
            'FRONT_DESK' => [
                'label' => 'Front Desk Operations',
                'description' => 'Front desk receptionist handling bookings, check-ins, and folios',
                'permissions' => ['manage-reservations', 'view-folio', 'process-checkout'],
            ],
            'ACCOUNTING' => [
                'label' => 'Accounting & Finance',
                'description' => 'Finance staff auditing invoices, payments, and sales reports',
                'permissions' => ['view-folio', 'process-checkout'],
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
                ['description' => $data['description']]
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
