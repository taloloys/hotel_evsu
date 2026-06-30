<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $primaryKey = 'order_id';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'order_number',
        'tab_id',
        'folio_id',
        'credit_account_id',
        'transaction_id',
        'customer_name',
        'room_number',
        'status',
        'discount_type',
        'discount_amount',
        'is_discount_percentage',
        'payment_method',
        'subtotal',
        'total',
        'user_id',
        'shift_id',
        'created_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'is_discount_percentage' => 'boolean',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'created_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'order_id', 'order_id');
    }

    public function tab(): BelongsTo
    {
        return $this->belongsTo(PosTab::class, 'tab_id', 'tab_id');
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class, 'folio_id', 'folio_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(CreditAccount::class, 'credit_account_id', 'account_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'shift_id');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
