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

    public function scopeByRoleAccess($query, $user)
    {
        if (! $user || ! $user->role) {
            return $query->whereRaw('1 = 0');
        }

        $roleName = $user->role->role_name;

        if (in_array($roleName, ['ADMIN', 'MANAGER', 'ACCOUNTING'])) {
            return $query;
        }

        if ($roleName === 'FRONT_DESK') {
            return $query->where(function ($q) {
                $q->where('funding_source', 'FRONT DESK')
                    ->orWhere('department', 'Front Office');
            });
        }

        if ($roleName === 'CAFETERIA') {
            return $query->where(function ($q) {
                $q->where('funding_source', 'CAFETERIA')
                    ->orWhere('department', 'Food & Beverage');
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public function scopeByPeriod($query, $period)
    {
        return match ($period) {
            'This Week' => $query->whereBetween('expense_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]),
            'Monthly' => $query->whereBetween('expense_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]),
            default => $query->where('expense_date', now()->toDateString()),
        };
    }
}
