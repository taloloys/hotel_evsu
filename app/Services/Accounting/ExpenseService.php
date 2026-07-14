<?php

namespace App\Services\Accounting;

use App\Models\ActivityLog;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * Create a new expense wrapped in a transaction.
     */
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::create([
                'expense_date' => $data['expense_date'],
                'department' => $data['department'],
                'purpose' => $data['purpose'],
                'category' => $data['category'],
                'status' => 'APPROVED', // Default to approved for immediate deduction
                'amount' => $data['amount'],
                'user_id' => auth()->id() ?? 1,
                'funding_source' => $data['funding_source'],
                'requested_by' => $data['requested_by'] ?? null,
            ]);

            ActivityLog::log(
                'ADD_CHARGE',
                "Recorded operating expense: {$expense->purpose} under {$expense->department} (Funded by {$expense->funding_source}) - ₱".number_format($expense->amount, 2)
            );

            return $expense;
        });
    }

    /**
     * Calculate total approved expenses for a specific funding source and time range.
     */
    public function calculateTotalExpensesForSource(string $source, ?Carbon $from = null, ?Carbon $to = null): float
    {
        $query = Expense::where('funding_source', $source)
            ->where('status', 'APPROVED');

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return (float) $query->sum('amount');
    }
}
