<?php

namespace App\Console\Commands;

use App\Services\RoomChargeService;
use Illuminate\Console\Command;

class PostNightlyRoomCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:post-nightly-room-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post pending nightly room charges for checked-in bookings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting catch-up process for checked-in bookings...');

        try {
            app(RoomChargeService::class)->processCatchUpCharges();
            $this->info('Successfully processed room charges for all active bookings.');
        } catch (\Exception $e) {
            $this->error("Failed to process room charges: {$e->getMessage()}");
        }

        $this->info('Completed posting nightly room charges.');

        return Command::SUCCESS;
    }
}
