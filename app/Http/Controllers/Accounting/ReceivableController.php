<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'ALL'); // ALL, CURRENT, OVERDUE, CRITICAL

        // We fetch open folios with bookings
        $query = Folio::where('status', 'OPEN')
            ->withBalances()
            ->with(['guest', 'bookings.room']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_number', 'like', "%{$search}%")
                    ->orWhereHas('guest', function ($g) use ($search) {
                        $g->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bookings.room', function ($r) use ($search) {
                        $r->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        $now = Carbon::now();

        // Fetch ALL folios (no pagination) for accurate KPI totals
        $allFolios = (clone $query)->orderBy('folio_id', 'desc')->get();

        $allReceivables = $allFolios->map(function ($folio) use ($now) {
            $balance = $folio->balance;
            $arrivalDate = $folio->bookings->first() ? Carbon::parse($folio->bookings->first()->arrival_date) : $now;
            $daysOld = $now->diffInDays($arrivalDate);

            if ($daysOld <= 30) {
                $status = 'Current';
            } elseif ($daysOld <= 60) {
                $status = 'Overdue';
            } else {
                $status = 'Critical';
            }

            return (object) ['balance' => $balance, 'status' => $status];
        })->filter(fn ($r) => $r->balance > 0);

        // Compute KPIs from ALL receivables
        $totalReceivables = $allReceivables->sum('balance');
        $currentReceivables = $allReceivables->filter(fn ($r) => $r->status === 'Current')->sum('balance');
        $overdueReceivables = $allReceivables->filter(fn ($r) => $r->status === 'Overdue')->sum('balance');
        $criticalReceivables = $allReceivables->filter(fn ($r) => $r->status === 'Critical')->sum('balance');

        // Paginate folios for the list display
        $folios = $query->orderBy('folio_id', 'desc')->paginate(15)->withQueryString();

        // Map and compute balances and age (current page only)
        $receivables = $folios->getCollection()->map(function ($folio) use ($now) {
            $totalCharges = $folio->total_charges;
            $totalCredits = $folio->total_credits;
            $balance = $folio->balance;

            // Age is based on the arrival date of the first booking or the creation date
            $arrivalDate = $folio->bookings->first() ? Carbon::parse($folio->bookings->first()->arrival_date) : $now;
            $daysOld = $now->diffInDays($arrivalDate);

            // Determine status based on age
            if ($daysOld <= 30) {
                $status = 'Current';
            } elseif ($daysOld <= 60) {
                $status = 'Overdue';
            } else {
                $status = 'Critical';
            }

            return (object) [
                'folio_id' => $folio->folio_id,
                'folio_number' => $folio->folio_number,
                'guest_name' => $folio->guest ? trim($folio->guest->first_name.' '.$folio->guest->last_name) : 'No Guest',
                'room_number' => $folio->bookings->first() && $folio->bookings->first()->room ? $folio->bookings->first()->room->room_number : 'N/A',
                'due_date' => $arrivalDate->addDays(30)->toDateString(), // 30 days due
                'days_old' => $daysOld,
                'status' => $status,
                'balance' => $balance,
            ];
        })->filter(fn ($folio) => $folio->balance > 0);

        // Apply aging status filter to page items
        if ($statusFilter !== 'ALL') {
            $receivables = $receivables->filter(function ($r) use ($statusFilter) {
                return strtoupper($r->status) === strtoupper($statusFilter);
            });
        }

        return view('accounting.receivables.index', [
            'receivables' => $receivables,
            'folios' => $folios,
            'totalReceivables' => $totalReceivables,
            'currentReceivables' => $currentReceivables,
            'overdueReceivables' => $overdueReceivables,
            'criticalReceivables' => $criticalReceivables,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }
}
