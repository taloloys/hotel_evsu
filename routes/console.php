<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Services\BackupSettingsService;

Schedule::command('app:post-nightly-room-charges')->daily();
Schedule::command('billing:post-daily-charges')->daily();
Schedule::command('shifts:close-orphaned')->everyFifteenMinutes();
Schedule::command('app:archive-old-data')->dailyAt('02:00');

$backupSettings = BackupSettingsService::get();
if ($backupSettings['enabled'] ?? false) {
    Schedule::command('db:auto-backup')->dailyAt($backupSettings['time'] ?? '02:00');
}

Schedule::command('db:clean-backups')->dailyAt('03:00');
