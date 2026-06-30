<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditAccount extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'account_name',
        'contact_name',
        'contact_number',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the ledger entries for this account.
     */
    public function ledgers()
    {
        return $this->hasMany(CreditAccountLedger::class, 'account_id', 'account_id');
    }

    /**
     * Calculate the outstanding balance dynamically from the ledger.
     * Charges add to the balance (debt), Payments reduce it.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        $charges = $this->ledgers()->where('type', 'charge')->sum('amount');
        $payments = $this->ledgers()->where('type', 'payment')->sum('amount');

        return (float) ($charges - $payments);
    }

    /**
     * Get the available credit limit dynamically.
     */
    public function getAvailableCreditAttribute(): float
    {
        return max(0, $this->credit_limit - $this->outstanding_balance);
    }
}
