<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folio extends Model
{
    protected $primaryKey = 'folio_id';

    public $timestamps = false;

    protected $fillable = [
        'folio_number',
        'registration_number',
        'account_number',
        'guest_id',
        'market_segment',
        'billing_arrangements',
        'special_arrangements',
        'num_pax',
        'has_joiner',
        'num_free_breakfasts',
        'breakfast_code',
        'symbol',
        'folio_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'has_joiner' => 'boolean',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'folio_id', 'folio_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'folio_id', 'folio_id');
    }
}
