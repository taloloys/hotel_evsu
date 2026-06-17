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
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'room_id', 'room_id');
    }
}
