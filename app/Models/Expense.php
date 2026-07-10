<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'expense_date',
        'department',
        'purpose',
        'category',
        'status',
        'amount',
        'user_id',
        'funding_source',
        'requested_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
