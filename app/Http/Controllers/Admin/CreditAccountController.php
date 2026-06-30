<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditAccount;
use App\Services\CreditBillingService;
use Illuminate\Http\Request;

class CreditAccountController extends Controller
{
    public function index()
    {
        $accounts = CreditAccount::withCount('ledgers')->get();

        return view('admin.credit-accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:150',
            'contact_name' => 'nullable|string|max:150',
            'contact_number' => 'nullable|string|max:50',
            'credit_limit' => 'required|numeric|min:0',
        ]);

        CreditAccount::create($validated);

        return back()->with('success', 'Credit Account created successfully.');
    }

    public function show(CreditAccount $account)
    {
        $account->load(['ledgers' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'ledgers.processedBy']);

        return view('admin.credit-accounts.show', compact('account'));
    }

    public function recordPayment(Request $request, CreditAccount $account, CreditBillingService $billingService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $billingService->recordPayment(
            $account,
            $request->amount,
            auth()->id() ?? 1,
            $request->notes
        );

        return back()->with('success', 'Payment recorded successfully.');
    }
}
