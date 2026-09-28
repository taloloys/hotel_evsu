<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Services\BackupSettingsService;
use App\Services\BackupStorageService;
use Illuminate\Console\Command;

class CleanBackupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-backups {--days= : The number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old database backups from configured backup disk';

    /**
     * Execute the console command.
     */
    public function handle(BackupStorageService $backupStorage): int
    {
        $settings = BackupSettingsService::get();
        $optionDays = $this->option('days');
        $days = $optionDays !== null ? (int) $optionDays : (int) ($settings['retention_days'] ?? 30);

        if ($days <= 0) {
            $days = 30;
        }

        $diskName = $backupStorage->getDiskName();
        $this->info("Cleaning up backups older than {$days} days on disk [{$diskName}]...");

        $deletedCount = $backupStorage->cleanup($days);

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
