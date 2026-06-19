<?php

namespace App\Models;

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
}
