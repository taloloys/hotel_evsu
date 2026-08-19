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
        // 1. Ensure Admin and system permissions are seeded
        $this->call(AdminSeeder::class);

        // 2. Create non-admin roles
        $roles = [
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
                    'is_system_admin' => false,
                ]
            );

            // Sync permissions for this role
            $rolePermissionIds = Permission::whereIn('permission_key', $data['permissions'])
                ->pluck('permission_id')
                ->toArray();

            $role->permissions()->sync($rolePermissionIds);
        }

        // 3. Create regular staff users
        $users = [
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

            if ($role) {
                User::firstOrCreate(
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
}
