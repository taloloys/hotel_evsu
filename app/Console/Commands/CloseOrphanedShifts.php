<?php

namespace App\Console\Commands;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloseOrphanedShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:close-orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close active shifts for users whose sessions have expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activeShifts = Shift::whereNull('end_time')->get();

        if ($activeShifts->isEmpty()) {
            $this->info('No active shifts found.');

            return;
        }

        $sessionLifetimeInMinutes = config('session.lifetime');
        $timeoutThreshold = now()->subMinutes($sessionLifetimeInMinutes)->getTimestamp();
        $closedCount = 0;

        foreach ($activeShifts as $shift) {
            // Check if the user has any active sessions
            $hasActiveSession = DB::table('sessions')
                ->where('user_id', $shift->user_id)
                ->where('last_activity', '>=', $timeoutThreshold)
                ->exists();

            if (! $hasActiveSession) {
                // Determine when to close it. We'll try to find their last session activity.
                // If not found, use current time.
                $lastSessionActivity = DB::table('sessions')
                    ->where('user_id', $shift->user_id)
                    ->orderByDesc('last_activity')
                    ->value('last_activity');

                $endTime = $lastSessionActivity ? Carbon::createFromTimestamp($lastSessionActivity) : now();

                $shift->update([
                    'end_time' => $endTime,
                ]);

                $closedCount++;
                $this->info("Closed shift {$shift->shift_id} for user {$shift->user_id}.");
            }
        }

        $this->info("Successfully closed {$closedCount} orphaned shift(s).");
    }
}
