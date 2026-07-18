<?php

namespace App\Console\Commands;

use App\Services\BackupSettingsService;
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

                return 1;
            }
        }

        $now = now();
        $sqlFilename = $now->format('Y-m-d_H-i-s').'_temp.sql';
        $zipFilename = $now->format('F j Y g-i A').'.zip';
        $sqlFilepath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$sqlFilename;
        $zipFilepath = rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$zipFilename;

        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath === ':memory:') {
                file_put_contents($sqlFilepath, '-- sqlite memory backup');
            } else {
                if (! file_exists($dbPath) || ! @copy($dbPath, $sqlFilepath)) {
                    $this->error('Backup failed: SQLite database file not found or not copyable.');
                    Log::error('Automatic backup failed: SQLite database file not found or not copyable.');

                    return 1;
                }
            }
        } else {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $mysqldump = $this->resolveBinary('mysqldump');
            $passwordArg = $password ? '-p'.escapeshellarg($password) : '';

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --result-file=%s %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passwordArg,
                escapeshellarg($sqlFilepath),
                escapeshellarg($database)
            );

            Log::info("Executing backup command: " . $command);
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $errorDetail = implode(' ', $output);
                $this->error("Backup failed: {$errorDetail}");
                Log::error("Automatic backup failed: {$errorDetail}");
                @unlink($sqlFilepath);

                return 1;
            }
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

            return 1;
        }

        $this->info("Backup created successfully at: {$zipFilepath}");
        Log::info("Automatic database backup created successfully: {$zipFilename}");

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
