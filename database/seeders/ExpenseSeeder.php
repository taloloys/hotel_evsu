<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first() ?? User::first();
        $userId = $admin ? $admin->user_id : 1;

        $expenses = [
            [
                'expense_date' => '2026-06-18',
                'department' => 'Housekeeping',
                'description' => 'Cleaning Supplies',
                'category' => 'Supplies',
                'status' => 'APPROVED',
                'amount' => 2500.00,
                'user_id' => $userId,
            ],
            [
                'expense_date' => '2026-06-18',
                'department' => 'Maintenance',
                'description' => 'Aircon Repair',
                'category' => 'Repairs',
                'status' => 'PENDING',
                'amount' => 4800.00,
                'user_id' => $userId,
            ],
            [
                'expense_date' => '2026-06-17',
                'department' => 'HR',
                'description' => 'Staff Salaries',
                'category' => 'Payroll',
                'status' => 'APPROVED',
                'amount' => 13420.00,
                'user_id' => $userId,
            ],
            [
                'expense_date' => '2026-06-16',
                'department' => 'Utilities',
                'description' => 'Meralco Electricity Bill',
                'category' => 'Utilities',
                'status' => 'APPROVED',
                'amount' => 12500.00,
                'user_id' => $userId,
            ],
            [
                'expense_date' => '2026-06-15',
                'department' => 'Administration',
                'description' => 'Office Printer Ink & Stationery',
                'category' => 'Supplies',
                'status' => 'APPROVED',
                'amount' => 8200.00,
                'user_id' => $userId,
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
