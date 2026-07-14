<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CheckArchiveStatusListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Check if archiving has run in the last 7 days
        if (! Cache::has('last_archive_run')) {
            // It hasn't run recently, run it asynchronously to avoid blocking the login
            Artisan::queue('app:archive-old-data');

            // Set a temporary cache so we don't queue it multiple times in quick succession
            Cache::put('last_archive_run', now(), now()->addHours(1));
        }
    }
}
