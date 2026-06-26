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
        'payment_method',
        'net_rate',
    ];

    protected function casts(): array
    {
        return [
            'has_joiner' => 'boolean',
            'net_rate' => 'decimal:2',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id', 'guest_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'folio_id', 'folio_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'folio_id', 'folio_id');
    }

    /**
     * Get the total charges for this folio.
     */
    public function getTotalChargesAttribute(): float
    {
        if (array_key_exists('transactions_sum_charge_amount', $this->attributes)) {
            return (float) ($this->attributes['transactions_sum_charge_amount'] ?? 0.00);
        }

        return (float) $this->transactions->sum('charge_amount');
    }

    /**
     * Get the total credits (payments/refunds) for this folio.
     */
    public function getTotalCreditsAttribute(): float
    {
        if (array_key_exists('transactions_sum_credit_amount', $this->attributes)) {
            return (float) ($this->attributes['transactions_sum_credit_amount'] ?? 0.00);
        }

        return (float) $this->transactions->sum('credit_amount');
    }

    /**
     * Get the outstanding balance of this folio.
     */
    public function getBalanceAttribute(): float
    {
        return $this->total_charges - $this->total_credits;
    }

    /**
     * Check if the folio has been settled (balance is zero).
     */
    public function isSettled(): bool
    {
        return abs($this->balance) < 0.01;
    }

    /**
     * Scope query to eager load balances using DB sums.
     */
    public function scopeWithBalances($query)
    {
        return $query->withSum('transactions', 'charge_amount')
            ->withSum('transactions', 'credit_amount');
    }

    public function scopeGuestFolios($query)
    {
        return $query->where('folio_type', '!=', 'SYSTEM');
    }

    public function scopeSystemFolios($query)
    {
        return $query->where('folio_type', 'SYSTEM');
    }
}
