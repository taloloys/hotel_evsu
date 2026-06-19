<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $primaryKey = 'room_id';

    public $timestamps = false;

    protected $fillable = [
        'room_number',
        'room_type',
        'base_rate',
        'status',
        'is_active',
    ];

    protected $appends = ['floor'];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the room's floor dynamically based on its room number.
     */
    public function getFloorAttribute(): string
    {
        $numberOnly = preg_replace('/[^0-9]/', '', $this->room_number);
        if (empty($numberOnly)) {
            return 'Ground Floor';
        }

        $numberVal = (int) $numberOnly;
        if ($numberVal >= 100) {
            $floorVal = (int) ($numberVal / 100);
        } else {
            $floorVal = 1;
        }

        return match ($floorVal) {
            1 => '1st Floor',
            2 => '2nd Floor',
            3 => '3rd Floor',
            default => "{$floorVal}th Floor",
        };
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'room_id', 'room_id');
    }
}
