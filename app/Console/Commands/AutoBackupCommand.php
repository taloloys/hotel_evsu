<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Services\BackupSettingsService;
use App\Services\BackupStorageService;
use App\Services\DatabaseDumpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class AutoBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:auto-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform automatic database backup to the configured backup disk';

    /**
     * Execute the console command.
     */
    public function handle(BackupStorageService $backupStorage): int
    {
        $settings = BackupSettingsService::get();

        if (! ($settings['enabled'] ?? false)) {
            $this->info('Automatic backup is disabled.');

            return 0;
        }

        $now = now();
        $sqlFilename = $now->format('Y-m-d_H-i-s').'_temp.sql';
        $zipFilename = $now->format('F j Y g-i A').'.zip';

        $tempDir = storage_path('app/temp_auto_backup_'.uniqid());
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $tempSqlFilePath = $tempDir.DIRECTORY_SEPARATOR.$sqlFilename;
        $tempZipFilePath = $tempDir.DIRECTORY_SEPARATOR.$zipFilename;

        if (! DatabaseDumpService::dump($tempSqlFilePath)) {
            $this->error('Backup failed: Unable to export database tables.');
            Log::error('Automatic backup failed: Unable to export database tables.');
            $this->cleanTempDir($tempDir);

            $settings['last_backup_failed'] = true;
            BackupSettingsService::set($settings);

            return 1;
        }

        $finalFilePath = $tempSqlFilePath;
        $finalFilename = $sqlFilename;

        // If local disk and local folder configured, mirror SQL file for local inspection/testing
        if ($backupStorage->getDiskName() === 'local' && ! empty($settings['folder']) && is_dir($settings['folder'])) {
            @copy($tempSqlFilePath, rtrim($settings['folder'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$sqlFilename);
        }

        // Zip the SQL file if ZipArchive extension is available
        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($tempZipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFile($tempSqlFilePath, $sqlFilename);
                $zip->close();
                @unlink($tempSqlFilePath);
                $finalFilePath = $tempZipFilePath;
                $finalFilename = $zipFilename;
            }
        }

        // Store to configured backup disk
        $stored = $backupStorage->store($finalFilePath, $finalFilename);
        $this->cleanTempDir($tempDir);

        if (! $stored) {
            $this->error('Backup failed: Unable to store backup artifact to configured backup disk.');
            Log::error('Automatic backup failed: Unable to store backup artifact to disk.');

            $settings['last_backup_failed'] = true;
            BackupSettingsService::set($settings);

            return 1;
        }

        $this->info("Backup created successfully: {$finalFilename}");
        Log::info("Automatic database backup created successfully: {$finalFilename}");

        ActivityLog::log(
            'DATABASE_BACKUP',
            "Automated scheduled backup created: {$finalFilename}"
        );

        $settings['last_backup_failed'] = false;
        BackupSettingsService::set($settings);

        $this->call('db:clean-backups');

        return 0;
    }

    private function cleanTempDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = @scandir($dir) ?: [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                @unlink($dir.DIRECTORY_SEPARATOR.$file);
            }
        }
        @rmdir($dir);
    }
}
