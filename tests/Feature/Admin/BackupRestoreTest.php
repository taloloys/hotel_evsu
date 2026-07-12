<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    $this->manageUsersPermission = Permission::create([
        'permission_key' => 'manage-users',
        'description' => 'Manage users',
        'module' => 'System',
        'is_active' => true,
    ]);

    $this->adminRole->permissions()->sync([$this->manageUsersPermission->permission_id]);

    $this->adminUser = User::factory()->create([
        'username' => 'admin_test',
        'full_name' => 'Admin User',
        'role_id' => $this->adminRole->role_id,
        'is_active' => true,
    ]);

    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $this->staffUser = User::factory()->create([
        'username' => 'staff_test',
        'full_name' => 'Front Desk Person',
        'role_id' => $this->staffRole->role_id,
        'is_active' => true,
    ]);
});

test('unauthenticated user is redirected from backup-restore page', function (): void {
    $this->get(route('admin.backup-restore'))
        ->assertRedirect(route('home'));
});

test('admin can view the backup-restore page', function (): void {
    $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore'))
        ->assertOk()
        ->assertSee('Create Backup')
        ->assertSee('Restore Database');
});

test('staff without manage-users permission is forbidden from backup-restore page', function (): void {
    $this->actingAs($this->staffUser)
        ->get(route('admin.backup-restore'))
        ->assertForbidden();
});

test('backup route requires manage-users permission', function (): void {
    $this->actingAs($this->staffUser)
        ->get(route('admin.backup-restore.backup'))
        ->assertForbidden();
});

test('restore route requires manage-users permission', function (): void {
    $this->actingAs($this->staffUser)
        ->post(route('admin.backup-restore.restore'))
        ->assertForbidden();
});

test('restore rejects non-sql file upload', function (): void {
    $file = UploadedFile::fake()->create('not-a-sql.exe', 100, 'application/octet-stream');

    $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.restore'), [
            'backup_file' => $file,
        ])
        ->assertSessionHasErrors('backup_file');
});

test('restore requires a file to be uploaded', function (): void {
    $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.restore'), [])
        ->assertSessionHasErrors('backup_file');
});

test('sidebar shows Backup and Restore link for admin user', function (): void {
    $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore'))
        ->assertOk()
        ->assertSee('Backup &amp; Restore', false);
});
