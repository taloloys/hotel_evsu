<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';

    public const CREATED_AT = 'timestamp';

    public const UPDATED_AT = null;

    protected $fillable = [
        'folio_id',
        'charge_code',
        'shift_id',
        'user_id',
        'transaction_date',
        'charge_number',
        'payment_method',
        'reference_notes',
        'charge_amount',
        'credit_amount',
        'department',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'charge_amount' => 'decimal:2',
            'credit_amount' => 'decimal:2',
            'timestamp' => 'datetime',
        ];
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class, 'folio_id', 'folio_id');
    }

    public function chargeCode(): BelongsTo
    {
        return $this->belongsTo(ChargeCode::class, 'charge_code', 'charge_code');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
