<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Services\BackupSettingsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanBackupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-backups {--days=30 : The number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old database backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $backupDir = BackupSettingsService::get()['folder'] ?? storage_path('backups');

        if (! is_dir($backupDir)) {
            $this->info("Backup directory does not exist: {$backupDir}");

            return 0;
        }

        $files = scandir($backupDir);
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            // Exclude safety backups from date-based cleanup (or include them? Probably include them)
            if (str_ends_with($file, '.sql') || str_ends_with($file, '.zip')) {
                $filePath = $backupDir.DIRECTORY_SEPARATOR.$file;
                $fileTime = Carbon::createFromTimestamp(filemtime($filePath));

                if ($fileTime->copy()->addDays($days)->isPast()) {
                    @unlink($filePath);
                    $deletedCount++;
                    $this->line("Deleted old backup: {$file}");
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} old backup(s).");
            ActivityLog::log(
                'DATABASE_BACKUP_DELETE',
                "Automated cleanup deleted {$deletedCount} backup(s) older than {$days} days."
            );
        } else {
            $this->info('No old backups found to delete.');
        }

        return 0;
    }
}
