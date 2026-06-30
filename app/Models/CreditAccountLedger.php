<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditAccountLedger extends Model
{
    use HasFactory;

    protected $primaryKey = 'ledger_id';

    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'processed_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the account this ledger entry belongs to.
     */
    public function account()
    {
        return $this->belongsTo(CreditAccount::class, 'account_id', 'account_id');
    }

    /**
     * Get the user who processed this transaction.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }
}
