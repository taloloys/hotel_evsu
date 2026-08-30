<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ChargeCodeResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PostDailyRoomCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:post-daily-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically posts daily room charges at midnight for open stays.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily room charge posting...');

        // 1. Ensure system user exists
        $systemUser = User::firstOrCreate(
            ['username' => 'system'],
            [
                'full_name' => 'System',
                'password_hash' => Hash::make('system_secure_pass_123'),
                'role_id' => Role::where('role_name', 'ADMIN')->value('role_id') ?? 1,
                'is_active' => true,
            ]
        );

        // 2. Fetch all checked-in bookings with open stays
        $bookings = Booking::where('status', 'CHECKED_IN')
            ->whereNull('departure_date')
            ->get();

        $this->info('Found '.$bookings->count().' active open-stay booking(s).');

        $activeShift = Shift::where('user_id', $systemUser->user_id)
            ->whereNull('end_time')
            ->first();

        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $systemUser->user_id,
                    'start_time' => Carbon::now(),
                ]);
            }
        }

        // Resolve room charge code dynamically by slug
        $roomChargeCode = ChargeCodeResolver::resolve(ChargeCodeResolver::ROOM_CHARGE);
        if ($roomChargeCode === null) {
            $this->error('Room charge code slug (room_charge) is not configured. Aborting.');

            return;
        }

        $today = Carbon::today();

        DB::transaction(function () use ($bookings, $systemUser, $activeShift) {
            foreach ($bookings as $booking) {
                $arrival = $booking->arrival_date;
                if (! $arrival) {
                    continue;
                }

                // We charge for each night stayed up to yesterday
                $yesterday = Carbon::yesterday();
                $nights = $arrival->diffInDays($yesterday);
                if ($nights < 0) {
                    continue; // Checked in today, first charge will occur at midnight tomorrow
                }

                $folio = $booking->folio;
                if (! $folio) {
                    continue;
                }

                $rate = ($folio->net_rate !== null) ? $folio->net_rate : ($booking->room?->base_rate ?? 0.00);

                // Charge each night from arrival up to yesterday
                for ($i = 0; $i <= $nights; $i++) {
                    $chargeDate = $arrival->copy()->addDays($i)->toDateString();

                    // Unique charge number for this specific night
                    $chargeNo = 'RM-'.$booking->booking_id.'-'.$chargeDate;

                    $exists = Transaction::where('folio_id', $booking->folio_id)
                        ->where('charge_number', $chargeNo)
                        ->exists();

                    if (! $exists) {
                        Transaction::create([
                            'folio_id' => $booking->folio_id,
                            'charge_code' => $roomChargeCode, // resolved via slug
                            'shift_id' => $activeShift->shift_id,
                            'user_id' => $systemUser->user_id,
                            'transaction_date' => $chargeDate,
                            'charge_number' => $chargeNo,
                            'payment_method' => 'NONE',
                            'reference_notes' => 'Room charge (Open Stay) for Date: '.$chargeDate.' (System Generated)',
                            'charge_amount' => $rate,
                            'credit_amount' => 0.00,
                        ]);

                        ActivityLog::log(
                            'ADD_CHARGE',
                            'Automatically posted open-stay room charge of ₱'.number_format($rate, 2)." on Folio #{$folio->folio_number} (Room {$booking->room?->room_number}) for night of {$chargeDate}."
                        );
                    }
                }
            }
        });

        $this->info('Daily room charge posting completed.');
    }
}
