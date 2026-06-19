<?php

namespace App\Models;

use Database\Factories\ShiftScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShiftSchedule extends Model
{
    /** @use HasFactory<ShiftScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_name',
        'shift_date',
        'scheduled_start_time',
        'scheduled_end_time',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'schedule_id', 'id');
    }

    public function actualShift(): HasOne
    {
        return $this->hasOne(Shift::class, 'schedule_id', 'id');
    }
}
