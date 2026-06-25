<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-accounting-billing');

        $statusFilter = $request->input('status', 'ALL');
        $search = $request->input('search');

        $query = Folio::with(['guest', 'bookings.room', 'transactions']);

        // Search filter (folio number, guest first/last name)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_number', 'like', "%{$search}%")
                    ->orWhereHas('guest', function ($gQ) use ($search) {
                        $gQ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($statusFilter !== 'ALL') {
            if ($statusFilter === 'CLOSED') {
                $query->where('status', 'CLOSED');
            } elseif ($statusFilter === 'OPEN') {
                $query->where('status', 'OPEN');
            }
        }

        $folios = $query->orderBy('folio_id', 'desc')->get();

        // Map computed fields
        $folios = $folios->map(function ($folio) {
            $totalCharges = $folio->transactions->sum('charge_amount');
            $totalCredits = $folio->transactions->sum('credit_amount');
            $balance = $totalCharges - $totalCredits;

            // Determine display status
            if ($folio->status === 'CLOSED') {
                $displayStatus = 'Paid';
            } elseif ($balance <= 0) {
                $displayStatus = 'Paid';
            } else {
                $displayStatus = 'Unpaid';
            }

            return (object) [
                'folio_id' => $folio->folio_id,
                'folio_number' => $folio->folio_number,
                'guest_name' => $folio->guest ? trim($folio->guest->first_name.' '.$folio->guest->last_name) : 'No Guest',
                'room_number' => $folio->bookings->first() && $folio->bookings->first()->room ? $folio->bookings->first()->room->room_number : 'N/A',
                'date' => $folio->bookings->first() ? $folio->bookings->first()->arrival_date->toDateString() : 'N/A',
                'display_status' => $displayStatus,
                'total_amount' => $totalCharges,
                'balance' => $balance,
                'status' => $folio->status,
            ];
        });

        // If filtering by specific balance status that is not simple database status
        if ($statusFilter === 'PAID') {
            $folios = $folios->filter(fn ($f) => $f->display_status === 'Paid');
        } elseif ($statusFilter === 'UNPAID') {
            $folios = $folios->filter(fn ($f) => $f->display_status === 'Unpaid');
        }

        // Calculate KPIs
        $totalInvoicesCount = Folio::count();

        // Paid invoices
        $paidCount = Folio::where('status', 'CLOSED')->count(); // simplified

        // Open/Pending invoices
        $pendingCount = Folio::where('status', 'OPEN')->count();

        // Unpaid balance (sum of charges - credits on open folios)
        $unpaidBalance = $folios->filter(fn ($f) => $f->status === 'OPEN')->sum('balance');

        return view('accounting.billing.index', [
            'folios' => $folios,
            'totalInvoices' => $totalInvoicesCount,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'unpaidBalance' => $unpaidBalance,
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    public function show(Folio $folio): View
    {
        Gate::authorize('manage-accounting-billing');

        $folio->load(['guest', 'bookings.room', 'transactions.chargeCode', 'transactions.user']);

        $totalCharges = $folio->transactions->sum('charge_amount');
        $totalCredits = $folio->transactions->sum('credit_amount');
        $balance = $totalCharges - $totalCredits;

        return view('accounting.billing.show', [
            'folio' => $folio,
            'totalCharges' => $totalCharges,
            'totalCredits' => $totalCredits,
            'balance' => $balance,
        ]);
    }
}
