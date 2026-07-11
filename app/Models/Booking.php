<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    public $timestamps = false;

    protected $fillable = [
        'folio_id',
        'room_id',
        'arrival_date',
        'arrival_time',
        'departure_date',
        'departure_time',
        'actual_check_in',
        'actual_check_out',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'arrival_date' => 'date',
            'departure_date' => 'date',
            'actual_check_in' => 'datetime',
            'actual_check_out' => 'datetime',
        ];
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class, 'folio_id', 'folio_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Post room charges night-by-night automatically.
     */
    public function postRoomCharges(): void
    {
        $arrival = $this->arrival_date;
        $departure = $this->departure_date;

        if (! $arrival || ! $departure) {
            return;
        }

        $nights = $arrival->diffInDays($departure);

        if ($nights <= 0) {
            $nights = 1; // standard minimum 1 night
        }

        $folio = $this->folio ?? $this->folio()->first();
        $rate = ($folio && $folio->net_rate !== null) ? $folio->net_rate : ($this->room?->base_rate ?? 0.00);

        // Dynamic user fallback to prevent foreign key issues in tests or CLI
        $userId = auth()->id() ?: (User::where('username', 'system')->value('user_id') ?: (User::first()?->user_id ?: 1));

        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $userId,
                    'start_time' => Carbon::now(),
                ]);
            }
        }

        // Defensive charge code verification/creation
        $chargeCode = ChargeCode::where('charge_code', 100)->first();
        if (! $chargeCode) {
            ChargeCode::create([
                'charge_code' => 100,
                'description' => 'ROOM CHARGE',
                'category' => 'HOTEL',
                'is_active' => true,
            ]);
        }

        $totalCharged = 0.00;
        $newChargesCount = 0;

        for ($i = 0; $i < $nights; $i++) {
            $chargeDate = $arrival->copy()->addDays($i)->toDateString();

            $chargeNo = 'RM-'.$this->booking_id.'-'.($i + 1);

            $exists = Transaction::where('folio_id', $this->folio_id)
                ->where('charge_number', $chargeNo)
                ->exists();

            if (! $exists) {
                Transaction::create([
                    'folio_id' => $this->folio_id,
                    'charge_code' => 100, // ROOM CHARGE
                    'shift_id' => $activeShift->shift_id,
                    'user_id' => $userId,
                    'transaction_date' => $chargeDate,
                    'charge_number' => $chargeNo,
                    'payment_method' => 'NONE',
                    'reference_notes' => 'Room charge for Night '.($i + 1)." (Date: {$chargeDate})",
                    'charge_amount' => $rate,
                    'credit_amount' => 0.00,
                ]);
                $totalCharged += $rate;
                $newChargesCount++;
            }
        }

        if ($newChargesCount > 0) {
            ActivityLog::log(
                'ADD_CHARGE',
                "Automatically posted {$newChargesCount} nights of room charges totaling ₱".number_format($totalCharged, 2)." on Folio #{$folio->folio_number} (Booking #{$this->booking_id})."
            );
        }
    }
}
