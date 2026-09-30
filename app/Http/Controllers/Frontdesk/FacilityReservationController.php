<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Mail\FacilityReservationApprovedMail;
use App\Mail\FacilityReservationRejectedMail;
use App\Models\ActivityLog;
use App\Models\FacilityReservation;
use App\Services\FacilityBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class FacilityReservationController extends Controller
{
    public function __construct(private readonly FacilityBookingService $bookingService) {}

    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $reservations = FacilityReservation::with('facility')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $pendingCount = FacilityReservation::where('status', 'pending')->count();

        return view('frontdesk.facility-reservations.index', compact('reservations', 'status', 'pendingCount'));
    }

    public function show(FacilityReservation $reservation): View
    {
        $reservation->load('facility');

        // Check if approving this would create a conflict (admin caution)
        $hasConflict = $this->bookingService->hasConflict(
            $reservation->facility_id,
            $reservation->reservation_date->toDateString(),
            substr($reservation->start_time, 0, 5),
            substr($reservation->end_time, 0, 5),
            $reservation->reservation_id,
            ['approved']
        );

        return view('frontdesk.facility-reservations.show', compact('reservation', 'hasConflict'));
    }

    public function approve(FacilityReservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be approved.');
        }

        // Re-check conflict inside a transaction with a row lock (race-condition guard)
        $approved = DB::transaction(function () use ($reservation) {
            $locked = FacilityReservation::lockForUpdate()->findOrFail($reservation->reservation_id);

            if ($this->bookingService->hasConflict(
                $locked->facility_id,
                $locked->reservation_date->toDateString(),
                substr($locked->start_time, 0, 5),
                substr($locked->end_time, 0, 5),
                $locked->reservation_id,
                ['approved']
            )) {
                return false;
            }

            $locked->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            return $locked;
        });

        if (! $approved) {
            return back()->with('error', 'Cannot approve — the time slot conflicts with an existing approved reservation.');
        }

        Mail::to($approved->booker_email)->queue(new FacilityReservationApprovedMail($approved));
        ActivityLog::log('FACILITY_RESERVATION_APPROVED', "Approved facility reservation #{$approved->reference_number}.");

        return redirect()->route('frontdesk.facility-reservations.index')
            ->with('success', "Reservation {$approved->reference_number} approved. Confirmation email queued.");
    }

    public function reject(Request $request, FacilityReservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be rejected.');
        }

        $reservation->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        Mail::to($reservation->booker_email)->queue(new FacilityReservationRejectedMail($reservation));
        ActivityLog::log('FACILITY_RESERVATION_REJECTED', "Rejected facility reservation #{$reservation->reference_number}.");

        return redirect()->route('frontdesk.facility-reservations.index')
            ->with('success', "Reservation {$reservation->reference_number} rejected.");
    }
}
