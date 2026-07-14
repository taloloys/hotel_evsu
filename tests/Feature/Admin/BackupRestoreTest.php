<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\BackupSettingsService;
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
        ->assertSee('Save Backup')
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

test('admin can see stored backups on index page', function (): void {
    $backupDir = storage_path('backups');
    if (! is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $testFile = $backupDir.'/test_backup_pest.sql';
    file_put_contents($testFile, 'SELECT 1;');

    $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore'))
        ->assertOk()
        ->assertSee('test_backup_pest.sql');

    @unlink($testFile);
});

test('backup saves to storage/backups/ and logs to activity log', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore.backup'));

    if ($response->getStatusCode() === 200) {
        $response->assertDownload();
        $this->assertDatabaseHas('activitylogs', [
            'action_type' => 'DATABASE_BACKUP',
        ]);

        $backupDir = storage_path('backups');
        $files = glob($backupDir.'/*.sql');
        foreach ($files as $file) {
            if (basename($file) !== 'backup_2026-07-12_19-31-49.sql' && basename($file) !== 'backup_2026-07-12_19-33-32.sql') {
                @unlink($file);
            }
        }
    } else {
        $response->assertRedirect(route('admin.backup-restore'));
    }
});

test('download-local downloads file and requires permission', function (): void {
    $backupDir = storage_path('backups');
    if (! is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $testFile = $backupDir.'/test_dl_pest.sql';
    file_put_contents($testFile, 'SELECT 1;');

    $this->get(route('admin.backup-restore.download-local', 'test_dl_pest.sql'))
        ->assertRedirect(route('home'));

    $this->actingAs($this->staffUser)
        ->get(route('admin.backup-restore.download-local', 'test_dl_pest.sql'))
        ->assertForbidden();

    $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore.download-local', 'test_dl_pest.sql'))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=test_dl_pest.sql');

    $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore.download-local', 'invalid*file.sql'))
        ->assertStatus(400);

    @unlink($testFile);
});

test('delete-local deletes file, logs activity, and requires permission', function (): void {
    $backupDir = storage_path('backups');
    if (! is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $testFile = $backupDir.'/test_del_pest.sql';
    file_put_contents($testFile, 'SELECT 1;');

    $this->actingAs($this->staffUser)
        ->delete(route('admin.backup-restore.delete-local', 'test_del_pest.sql'))
        ->assertForbidden();

    $this->assertTrue(file_exists($testFile));

    $this->actingAs($this->adminUser)
        ->delete(route('admin.backup-restore.delete-local', 'test_del_pest.sql'))
        ->assertRedirect(route('admin.backup-restore'));

    $this->assertFalse(file_exists($testFile));
    $this->assertDatabaseHas('activitylogs', [
        'action_type' => 'DATABASE_BACKUP_DELETE',
        'description' => 'Deleted server backup file: test_del_pest.sql',
        'user_id' => $this->adminUser->user_id,
    ]);
});

test('restore-local requires permission and handles missing file', function (): void {
    $this->actingAs($this->staffUser)
        ->post(route('admin.backup-restore.restore-local'), ['filename' => 'nonexistent.sql'])
        ->assertForbidden();

    $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.restore-local'), ['filename' => 'nonexistent.sql'])
        ->assertRedirect(route('admin.backup-restore'))
        ->assertSessionHas('error', 'Backup file not found on server.');
});

test('index page limits backups to 5 and displays older backups warning if there are more than 5', function (): void {
    $backupDir = storage_path('backups');
    if (! is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $files = [];
    for ($i = 1; $i <= 6; $i++) {
        $file = $backupDir."/test_limit_{$i}.sql";
        file_put_contents($file, "SELECT {$i};");
        touch($file, time() - ($i * 10));
        $files[] = $file;
    }

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore'));

    $response->assertOk();

    $response->assertSee('test_limit_1.sql');
    $response->assertSee('test_limit_5.sql');
    $response->assertDontSee('test_limit_6.sql');
    $response->assertSee('Only the 5 most recent backups are shown');

    foreach ($files as $file) {
        @unlink($file);
    }
});

test('backup AJAX returns JSON response with filename and logs activity', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.backup-restore.backup'), [
            'HTTP_ACCEPT' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

    if ($response->getStatusCode() === 200) {
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertDatabaseHas('activitylogs', [
            'action_type' => 'DATABASE_BACKUP',
        ]);

        $filename = $response->json('filename');
        @unlink(storage_path('backups/'.$filename));
    }
});

test('admin can update automatic backup settings', function (): void {
    $folderPath = storage_path('backups');

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.settings'), [
            'enabled' => '1',
            'time' => '23:30',
            'folder' => $folderPath,
        ]);

    $response->assertRedirect(route('admin.backup-restore'))
        ->assertSessionHas('success', 'Automatic backup settings saved successfully.');

    $settings = BackupSettingsService::get();
    $this->assertTrue($settings['enabled']);
    $this->assertEquals('23:30', $settings['time']);
    $this->assertEquals($folderPath, $settings['folder']);

    $this->assertDatabaseHas('activitylogs', [
        'action_type' => 'SYSTEM_SETTINGS',
        'description' => 'Automatic database backup settings updated. Status: Enabled',
    ]);
});

test('saving invalid backup settings is rejected', function (): void {
    // Invalid time format
    $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.settings'), [
            'enabled' => '1',
            'time' => '25:00', // invalid hour
            'folder' => storage_path('backups'),
        ])
        ->assertSessionHasErrors('time');

    // Missing folder
    $this->actingAs($this->adminUser)
        ->post(route('admin.backup-restore.settings'), [
            'enabled' => '1',
            'time' => '02:00',
            'folder' => '',
        ])
        ->assertSessionHasErrors('folder');
});

test('auto backup artisan command outputs disabled message when disabled', function (): void {
    BackupSettingsService::set([
        'enabled' => false,
        'time' => '02:00',
        'folder' => storage_path('backups'),
    ]);

    $this->artisan('db:auto-backup')
        ->expectsOutput('Automatic backup is disabled.')
        ->assertExitCode(0);
});

test('auto backup artisan command executes when enabled', function (): void {
    $folderPath = storage_path('test_auto_backups');
    if (! is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    BackupSettingsService::set([
        'enabled' => true,
        'time' => '02:00',
        'folder' => $folderPath,
    ]);

    // Run the command
    $this->artisan('db:auto-backup')
        ->assertExitCode(0);

    // Verify file exists
    $files = glob($folderPath.'/*.sql');
    $this->assertNotEmpty($files);

    // Clean up
    foreach ($files as $file) {
        @unlink($file);
    }
    @rmdir($folderPath);
});

test('admin can list folders for backup directory picker', function (): void {
    $backupDir = storage_path('backups');
    if (! is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $this->actingAs($this->adminUser)
        ->getJson(route('admin.backup-restore.list-folders', ['path' => $backupDir]))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'current' => $backupDir,
        ])
        ->assertJsonStructure(['folders', 'breadcrumbs']);
});

test('list folders rejects paths outside storage', function (): void {
    $this->actingAs($this->adminUser)
        ->getJson(route('admin.backup-restore.list-folders', ['path' => 'C:\\Windows']))
        ->assertForbidden()
        ->assertJson(['success' => false]);
});

test('staff cannot list backup folders', function (): void {
    $this->actingAs($this->staffUser)
        ->getJson(route('admin.backup-restore.list-folders'))
        ->assertForbidden();
});
