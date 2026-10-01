<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Mail\FacilityReservationApprovedMail;
use App\Mail\FacilityReservationRejectedMail;
use App\Models\ActivityLog;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Services\FacilityBookingService;
use Carbon\Carbon;
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
        $facilityId = $request->query('facility') ?? $request->query('facility_id');

        $reservations = FacilityReservation::with('facility')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($facilityId, fn ($q) => $q->where('facility_id', $facilityId))
            ->latest()
            ->paginate(20);

        $pendingCount = FacilityReservation::where('status', 'pending')->count();

        return view('frontdesk.facility-reservations.index', compact('reservations', 'status', 'pendingCount', 'facilityId'));
    }

    public function create(Request $request): View
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $selectedFacilityId = $request->query('facility') ?? $request->query('facility_id');

        return view('frontdesk.facility-reservations.create', compact('facilities', 'selectedFacilityId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,facility_id'],
            'booker_name' => ['required', 'string', 'max:255'],
            'booker_email' => ['required', 'email', 'max:255'],
            'booker_contact' => ['required', 'string', 'max:30'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'event_details' => ['nullable', 'string', 'max:1000'],
            'billing_type' => ['nullable', 'in:hourly,daily'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:reservation_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'agreed_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:approved,pending'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $facility = Facility::findOrFail($validated['facility_id']);
        $billingType = $validated['billing_type'] ?? ($facility->rate_type ?? 'hourly');
        $endDate = $validated['end_date'] ?? $validated['reservation_date'];
        $agreedRate = (isset($validated['agreed_rate']) && $validated['agreed_rate'] !== null && $validated['agreed_rate'] !== '')
            ? (float) $validated['agreed_rate']
            : null;

        if ($this->bookingService->hasConflict(
            $facility->facility_id,
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time'],
            null,
            ['approved', 'active'],
            $endDate
        )) {
            return back()->withErrors([
                'reservation_date' => 'The selected facility already has an approved or active reservation that conflicts with this time slot.',
            ])->withInput();
        }

        $durationHours = $this->bookingService->computeDurationHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $startDateCarbon = Carbon::parse($validated['reservation_date']);
        $endDateCarbon = Carbon::parse($endDate);
        $totalDays = max(1, $startDateCarbon->diffInDays($endDateCarbon) + 1);

        $amount = $this->bookingService->calculateAmount(
            $facility,
            $validated['start_time'],
            $validated['end_time'],
            $billingType,
            $validated['reservation_date'],
            $endDate,
            $agreedRate
        );

        $isApproved = $validated['status'] === 'approved';

        $reservation = FacilityReservation::create([
            'facility_id' => $facility->facility_id,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_contact' => $validated['booker_contact'],
            'event_name' => $validated['event_name'] ?? null,
            'event_details' => $validated['event_details'] ?? null,
            'billing_type' => $billingType,
            'reservation_date' => $validated['reservation_date'],
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
            'duration_hours' => $durationHours,
            'agreed_rate' => $agreedRate,
            'estimated_amount' => $amount,
            'status' => $validated['status'],
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
            'processed_by' => $isApproved ? auth()->id() : null,
            'processed_at' => $isApproved ? now() : null,
        ]);

        if ($isApproved) {
            Mail::to($reservation->booker_email)->queue(new FacilityReservationApprovedMail($reservation));
        }

        ActivityLog::log('FACILITY_RESERVATION_CREATED', "Created facility reservation #{$reservation->reference_number} for {$reservation->booker_name}.");

        return redirect()->route('frontdesk.facility-reservations.show', $reservation)
            ->with('success', "Facility reservation #{$reservation->reference_number} has been created successfully.");
    }

    public function show(FacilityReservation $reservation): View
    {
        $reservation->load('facility');

        $endDate = $reservation->end_date ? $reservation->end_date->toDateString() : $reservation->reservation_date->toDateString();

        // Check if approving this would create a conflict (admin caution)
        $hasConflict = $this->bookingService->hasConflict(
            $reservation->facility_id,
            $reservation->reservation_date->toDateString(),
            substr($reservation->start_time, 0, 5),
            substr($reservation->end_time, 0, 5),
            $reservation->reservation_id,
            ['approved', 'active'],
            $endDate
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
            $endDate = $locked->end_date ? $locked->end_date->toDateString() : $locked->reservation_date->toDateString();

            if ($this->bookingService->hasConflict(
                $locked->facility_id,
                $locked->reservation_date->toDateString(),
                substr($locked->start_time, 0, 5),
                substr($locked->end_time, 0, 5),
                $locked->reservation_id,
                ['approved', 'active'],
                $endDate
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

    public function checkIn(FacilityReservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'approved') {
            return back()->with('error', 'Only approved reservations can be checked in.');
        }

        $reservation->update([
            'status' => 'active',
            'actual_start_time' => now(),
        ]);

        ActivityLog::log('FACILITY_RESERVATION_CHECKED_IN', "Checked in facility reservation #{$reservation->reference_number} for {$reservation->booker_name}.");

        return back()->with('success', "Reservation {$reservation->reference_number} is now Active (Checked In).");
    }

    public function extend(Request $request, FacilityReservation $reservation): RedirectResponse
    {
        if (! in_array($reservation->status, ['approved', 'active'])) {
            return back()->with('error', 'Only approved or active reservations can be extended.');
        }

        $validated = $request->validate([
            'end_date' => ['required', 'date', 'after_or_equal:'.$reservation->reservation_date->toDateString()],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        // Conflict check
        if ($this->bookingService->hasConflict(
            $reservation->facility_id,
            $reservation->reservation_date->toDateString(),
            substr($reservation->start_time, 0, 5),
            $validated['end_time'],
            $reservation->reservation_id,
            ['approved', 'active'],
            $validated['end_date']
        )) {
            return back()->with('error', 'Cannot extend — the requested new time/date slot conflicts with another reservation.');
        }

        $facility = $reservation->facility;
        $totalDays = Carbon::parse($reservation->reservation_date)->diffInDays(Carbon::parse($validated['end_date'])) + 1;
        $durationHours = $this->bookingService->computeDurationHours(
            substr($reservation->start_time, 0, 5),
            $validated['end_time']
        );

        $newAmount = $this->bookingService->calculateAmount(
            $facility,
            substr($reservation->start_time, 0, 5),
            $validated['end_time'],
            $reservation->billing_type ?? $facility->rate_type,
            $reservation->reservation_date->toDateString(),
            $validated['end_date'],
            $reservation->agreed_rate !== null ? (float) $reservation->agreed_rate : null
        );

        $reservation->update([
            'end_date' => $validated['end_date'],
            'end_time' => $validated['end_time'].':00',
            'total_days' => $totalDays,
            'duration_hours' => $durationHours,
            'estimated_amount' => $newAmount,
        ]);

        ActivityLog::log('FACILITY_RESERVATION_EXTENDED', "Extended facility reservation #{$reservation->reference_number} to {$validated['end_date']} {$validated['end_time']}.");

        return back()->with('success', "Reservation #{$reservation->reference_number} extended successfully. Updated amount: ₱".number_format($newAmount, 2));
    }

    public function timeOut(Request $request, FacilityReservation $reservation): RedirectResponse
    {
        if (! in_array($reservation->status, ['approved', 'active'])) {
            return back()->with('error', 'Only approved or active reservations can be timed out / checked out.');
        }

        $now = now();
        $reservation->load('facility');
        $charge = $this->bookingService->computeExcessCharge($reservation, $now);

        $reservation->update([
            'status' => 'completed',
            'actual_start_time' => $reservation->actual_start_time ?? $now,
            'actual_end_time' => $now,
            'final_amount' => $charge['final_amount'],
        ]);

        $msg = "Reservation #{$reservation->reference_number} has been timed out (completed).";
        if ($charge['excess_hours'] > 0) {
            $msg .= " Excess usage detected: {$charge['excess_hours']} hrs (Additional ₱".number_format($charge['excess_charge'], 2).'). Total: ₱'.number_format($charge['final_amount'], 2);
        } else {
            $msg .= ' Final amount: ₱'.number_format($charge['final_amount'], 2);
        }

        ActivityLog::log('FACILITY_RESERVATION_TIMED_OUT', "Timed out facility reservation #{$reservation->reference_number}. Final amount: ₱{$charge['final_amount']}.");

        return back()->with('success', $msg);
    }

    public function cancel(Request $request, FacilityReservation $reservation): RedirectResponse
    {
        if (in_array($reservation->status, ['completed', 'cancelled', 'rejected'])) {
            return back()->with('error', 'This reservation is already closed or cancelled.');
        }

        $reason = $request->input('cancellation_notes') ?: $request->input('admin_notes');
        $existingNotes = $reservation->admin_notes ? $reservation->admin_notes."\n" : '';
        $newNotes = $existingNotes.'[Cancelled on '.now()->format('M d, Y h:i A').' by '.(auth()->user()?->full_name ?? 'Staff').']'.($reason ? ': '.$reason : '');

        $reservation->update([
            'status' => 'cancelled',
            'admin_notes' => $newNotes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        ActivityLog::log('FACILITY_RESERVATION_CANCELLED', "Cancelled facility reservation #{$reservation->reference_number}.");

        return back()->with('success', "Facility reservation #{$reservation->reference_number} has been cancelled.");
    }
}
