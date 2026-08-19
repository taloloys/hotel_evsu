<?php

namespace App\Console\Commands;

use App\Services\BackupSettingsService;
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
    protected $description = 'Perform automatic database backup to the configured folder';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $settings = BackupSettingsService::get();

        if (! ($settings['enabled'] ?? false)) {
            $this->info('Automatic backup is disabled.');

            return 0;
        }

        $folder = $settings['folder'] ?? storage_path('backups');

        if (! is_dir($folder)) {
            if (! @mkdir($folder, 0755, true)) {
                $this->error("Failed to create backup directory: {$folder}");
                Log::error("Automatic backup failed: Unable to create backup directory {$folder}");

                $settings['last_backup_failed'] = true;
                BackupSettingsService::set($settings);

                return 1;
            }
        }

        $now = now();
        $sqlFilename = $now->format('Y-m-d_H-i-s').'_temp.sql';
        $zipFilename = $now->format('F j Y g-i A').'.zip';
        $sqlFilepath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$sqlFilename;
        $zipFilepath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$zipFilename;

        $connection = config('database.default');

        if (! DatabaseDumpService::dump($sqlFilepath)) {
            $this->error('Backup failed: Unable to export database tables.');
            Log::error('Automatic backup failed: Unable to export database tables.');
            @unlink($sqlFilepath);

            $settings['last_backup_failed'] = true;
            BackupSettingsService::set($settings);

            return 1;
        }

        // Zip the SQL file
        $zip = new ZipArchive;
        if ($zip->open($zipFilepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($sqlFilepath, $sqlFilename);
            $zip->close();
            @unlink($sqlFilepath); // Delete the original SQL file to save space
        } else {
            $this->error("Backup failed: Unable to create ZIP file at {$zipFilepath}");
            Log::error("Automatic backup failed: Unable to create ZIP file at {$zipFilepath}");
            @unlink($sqlFilepath);

            $settings['last_backup_failed'] = true;
            BackupSettingsService::set($settings);

            return 1;
        }

        $this->info("Backup created successfully at: {$zipFilepath}");
        Log::info("Automatic database backup created successfully: {$zipFilename}");

        $settings['last_backup_failed'] = false;
        BackupSettingsService::set($settings);

        $this->call('db:clean-backups');

        return 0;
    }

    /**
     * Resolve the full path to a MySQL binary (mysqldump or mysql),
     * falling back to common installation paths when not on system PATH.
     */
    private function resolveBinary(string $binary): string
    {
        $candidates = [
            // XAMPP (Windows)
            "C:\\xampp\\mysql\\bin\\{$binary}.exe",
            // WAMP64
            ...glob("C:\\wamp64\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            // Laragon
            ...glob("C:\\laragon\\bin\\mysql\\*\\bin\\{$binary}.exe") ?: [],
            // MAMP (Windows)
            "C:\\MAMP\\bin\\mysql\\bin\\{$binary}.exe",
            // Linux/macOS common paths
            "/usr/bin/{$binary}",
            "/usr/local/bin/{$binary}",
            "/opt/homebrew/bin/{$binary}",
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return $binary;
    }
}
