<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Add manage-facilities permission if it doesn't already exist
        $permission = Permission::firstOrCreate(
            ['permission_key' => 'manage-facilities'],
            [
                'description' => 'Create, edit, activate/deactivate, and view facilities',
                'module' => 'Front Desk',
                'is_active' => true,
            ]
        );

        // Attach to SUPER_ADMIN and ADMIN roles only (Front Desk can view/book but not manage)
        $roles = Role::whereIn('role_name', ['SUPER_ADMIN', 'ADMIN'])->get();
        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching([$permission->permission_id]);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('permission_key', 'manage-facilities')->first();
        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
