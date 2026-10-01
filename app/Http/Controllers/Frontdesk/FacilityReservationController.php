<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Mail\FacilityReservationApprovedMail;
use App\Mail\FacilityReservationRejectedMail;
use App\Models\ActivityLog;
use App\Models\Facility;
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
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:approved,pending'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $facility = Facility::findOrFail($validated['facility_id']);

        if ($this->bookingService->hasConflict(
            $facility->facility_id,
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return back()->withErrors([
                'reservation_date' => 'The selected facility already has an approved reservation that conflicts with this time slot.',
            ])->withInput();
        }

        $durationHours = $this->bookingService->computeDurationHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $amount = $this->bookingService->calculateAmount(
            $facility,
            $validated['start_time'],
            $validated['end_time']
        );

        $isApproved = $validated['status'] === 'approved';

        $reservation = FacilityReservation::create([
            'facility_id' => $facility->facility_id,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_contact' => $validated['booker_contact'],
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
            'duration_hours' => $durationHours,
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
