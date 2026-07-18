<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Folio;
use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $methodFilter = $request->input('method');

        $query = Transaction::where('credit_amount', '>', 0)
            ->with(['folio.guest', 'chargeCode']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_notes', 'like', "%{$search}%")
                    ->orWhere('charge_number', 'like', "%{$search}%")
                    ->orWhereHas('folio.guest', function ($g) use ($search) {
                        $g->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($methodFilter && $methodFilter !== 'All Methods') {
            $query->where('payment_method', strtoupper($methodFilter));
        }

        $payments = $query->orderBy('timestamp', 'desc')->paginate(15)->withQueryString();

        // Open folios for the "Record Payment" dropdown
        $openFolios = Folio::where('status', 'OPEN')->with('guest')->get();

        // Calculate KPI metrics
        $totalPayments = Transaction::sum('credit_amount');
        $cashPayments = Transaction::where('payment_method', 'CASH')->sum('credit_amount');
        $cardPayments = Transaction::where('payment_method', 'CREDIT_CARD')->sum('credit_amount');
        $pendingPayments = Transaction::where('payment_method', 'CHECK')->sum('credit_amount');

        return view('accounting.payments.index', [
            'payments' => $payments,
            'openFolios' => $openFolios,
            'totalPayments' => $totalPayments,
            'cashPayments' => $cashPayments,
            'cardPayments' => $cardPayments,
            'pendingPayments' => $pendingPayments,
            'search' => $search,
            'methodFilter' => $methodFilter,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'folio_id' => ['required', 'exists:folios,folio_id'],
            'payment_method' => ['required', 'in:CASH,CREDIT_CARD,CHECK'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $folio = Folio::findOrFail($request->folio_id);

        // Active shift logic
        $userId = auth()->id() ?? 1;
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        // If no active shift session exists, fetch latest shift in database or create one on the fly
        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $userId,
                    'start_time' => now(),
                ]);
            }
        }

        // Map method to charge_code
        // 403: CASH, 401: MASTERCARD (used for credit card), 402: VISA
        $chargeCode = 403;
        if ($request->payment_method === 'CREDIT_CARD') {
            $chargeCode = 401;
        }

        $refNo = 'PAY-'.time();

        Transaction::create([
            'folio_id' => $folio->folio_id,
            'charge_code' => $chargeCode,
            'shift_id' => $activeShift->shift_id,
            'user_id' => $userId,
            'transaction_date' => now()->toDateString(),
            'charge_number' => $request->reference_notes ?? $refNo,
            'payment_method' => $request->payment_method,
            'reference_notes' => 'Payment received: '.($request->reference_notes ?? 'No details'),
            'charge_amount' => 0.00,
            'credit_amount' => $request->amount,
        ]);

        ActivityLog::log(
            'ADD_CHARGE',
            'Recorded payment of ₱'.number_format($request->amount, 2)." via {$request->payment_method} on Folio #{$folio->folio_number}."
        );

        return redirect()->route('accounting.payments')->with('success', 'Payment recorded successfully!');
    }
}
