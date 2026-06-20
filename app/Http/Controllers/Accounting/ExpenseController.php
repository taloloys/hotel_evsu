<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $departmentFilter = $request->input('department');

        $query = Expense::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($departmentFilter && $departmentFilter !== 'All Departments') {
            $query->where('department', $departmentFilter);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Calculate KPIs
        $totalExpenses = Expense::where('status', 'APPROVED')->sum('amount');
        $utilities = Expense::where('status', 'APPROVED')->where('category', 'Utilities')->sum('amount');
        $salaries = Expense::where('status', 'APPROVED')->where('category', 'Payroll')->sum('amount');
        $supplies = Expense::where('status', 'APPROVED')->where('category', 'Supplies')->sum('amount');

        // Fetch distinct departments for the filter dropdown
        $departments = Expense::select('department')->distinct()->pluck('department');

        return view('accounting.expenses.index', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'utilities' => $utilities,
            'salaries' => $salaries,
            'supplies' => $supplies,
            'departments' => $departments,
            'search' => $search,
            'departmentFilter' => $departmentFilter,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'expense_date' => ['required', 'date'],
            'department' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $expense = Expense::create([
            'expense_date' => $request->expense_date,
            'department' => $request->department,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'APPROVED', // Approve directly by default in early stage
            'amount' => $request->amount,
            'user_id' => auth()->id() ?? 1,
        ]);

        ActivityLog::log(
            'ADD_CHARGE',
            "Recorded operating expense: {$request->description} under {$request->department} - ₱".number_format($request->amount, 2)
        );

        return redirect()->route('accounting.expenses')->with('success', 'Expense recorded successfully!');
    }

    public function approve(Expense $expense): RedirectResponse
    {
        $expense->update(['status' => 'APPROVED']);

        ActivityLog::log(
            'ADD_CHARGE',
            "Approved operating expense #{$expense->expense_id}: {$expense->description} - ₱".number_format($expense->amount, 2)
        );

        return redirect()->route('accounting.expenses')->with('success', 'Expense approved successfully!');
    }
}
