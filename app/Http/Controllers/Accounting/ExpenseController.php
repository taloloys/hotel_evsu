<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Services\Accounting\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $departmentFilter = $request->input('department');
        $periodFilter = $request->input('period', 'Today');
        $user = auth()->user();

        $query = Expense::with('user')
            ->byRoleAccess($user)
            ->byPeriod($periodFilter);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($departmentFilter && $departmentFilter !== 'All Departments') {
            $query->where('department', $departmentFilter);
        }

        // Calculate KPIs using the filtered query context
        $kpiQuery = clone $query;
        $totalExpenses = (clone $kpiQuery)->where('status', 'APPROVED')->sum('amount');
        $utilities = (clone $kpiQuery)->where('status', 'APPROVED')->where('category', 'Utilities')->sum('amount');
        $salaries = (clone $kpiQuery)->where('status', 'APPROVED')->where('category', 'Payroll')->sum('amount');
        $supplies = (clone $kpiQuery)->where('status', 'APPROVED')->where('category', 'Supplies')->sum('amount');

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(10)->withQueryString();

        // Fetch distinct departments for the filter dropdown
        $departments = Expense::byRoleAccess($user)->select('department')->distinct()->pluck('department');

        return view('accounting.expenses.index', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'utilities' => $utilities,
            'salaries' => $salaries,
            'supplies' => $supplies,
            'departments' => $departments,
            'search' => $search,
            'departmentFilter' => $departmentFilter,
            'periodFilter' => $periodFilter,
        ]);
    }

    public function store(Request $request, ExpenseService $expenseService): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role?->role_name, ['ADMIN', 'MANAGER', 'ACCOUNTING', 'FRONT_DESK', 'CAFETERIA'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'department' => ['required', 'string', 'in:Front Office,Housekeeping,Maintenance,Purchasing,Food & Beverage'],
            'purpose' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'funding_source' => ['required', 'string', 'in:FRONT DESK,CAFETERIA'],
            'requested_by' => ['required', 'string', 'max:100'],
        ]);

        $expenseService->createExpense($validated);

        return redirect()->route('accounting.expenses')->with('success', 'Expense recorded successfully!');
    }

    public function approve(Expense $expense): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role?->role_name, ['ADMIN', 'MANAGER', 'ACCOUNTING'])) {
            abort(403, 'Unauthorized action.');
        }

        $expense->update(['status' => 'APPROVED']);

        ActivityLog::log(
            'ADD_CHARGE',
            "Approved operating expense #{$expense->expense_id}: {$expense->purpose} - ₱".number_format($expense->amount, 2)
        );

        return redirect()->route('accounting.expenses')->with('success', 'Expense approved successfully!');
    }
}
