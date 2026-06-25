<?php

namespace App\Console\Commands;

use App\Models\Booking;
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
        $bookings = Booking::where('status', 'CHECKED_IN')->get();

        $this->info('Found '.$bookings->count().' checked-in bookings to process.');

        foreach ($bookings as $booking) {
            try {
                $booking->postRoomCharges();
                $this->line("Processed room charges for Booking #{$booking->booking_id}");
            } catch (\Exception $e) {
                $this->error("Failed to process room charges for Booking #{$booking->booking_id}: {$e->getMessage()}");
            }
        }

        $this->info('Completed posting nightly room charges.');

        return Command::SUCCESS;
    }
}
