<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Services\BackupSettingsService;
use Illuminate\Support\Facades\Mail;

Schedule::command('app:post-nightly-room-charges')->daily();
Schedule::command('shifts:close-orphaned')->everyFifteenMinutes();
Schedule::command('app:archive-old-data')->dailyAt('02:00');

$backupSettings = BackupSettingsService::get();
if ($backupSettings['enabled'] ?? false) {
    Schedule::command('db:auto-backup')->dailyAt($backupSettings['time'] ?? '02:00');
}

Schedule::command('db:clean-backups')->dailyAt('03:00');

Artisan::command('mail:test {email}', function ($email) {
    $this->info("Sending test email to {$email} via Brevo SMTP...");

    try {
        Mail::raw('Congratulations! Your Brevo SMTP mail integration with EVSU Hotel System is working perfectly locally and on Railway.', function ($message) use ($email) {
            $message->to($email)->subject('Brevo SMTP Test - EVSU Hotel System');
        });
        $this->info("✅ Email successfully sent to {$email}!");
    } catch (Throwable $e) {
        $this->error('❌ Failed to send email: '.$e->getMessage());
    }
})->purpose('Send a test email via Brevo SMTP to verify mail setup');
