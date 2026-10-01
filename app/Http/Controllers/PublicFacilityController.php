<?php

namespace App\Http\Controllers;

use App\Exceptions\FacilityReservationConflictException;
use App\Http\Requests\StoreFacilityBookingRequest;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use App\Models\SystemSetting;
use App\Services\FacilityBookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicFacilityController extends Controller
{
    public function __construct(private readonly FacilityBookingService $bookingService) {}

    public function show(Facility $facility): View
    {
        abort_unless($facility->is_active, 404);

        $facilityId = $facility->facility_id;
        $approvedReservations = FacilityReservation::whereIn('status', ['approved', 'active'])
            ->where(function ($facQ) use ($facilityId) {
                $facQ->where('facility_id', $facilityId)
                    ->orWhereHas('reservedFacilities', function ($sub) use ($facilityId) {
                        $sub->where('facilities.facility_id', $facilityId);
                    });
            })
            ->where('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get(['reservation_date', 'start_time', 'end_time']);

        return view('facilities.show', compact('facility', 'approvedReservations'));
    }

    public function bookForm(Facility $facility): View
    {
        abort_unless($facility->is_active, 404);

        $termsContent = SystemSetting::get('facility_terms_content', 'No terms configured yet.');

        return view('facilities.book', compact('facility', 'termsContent'));
    }

    public function submitBooking(StoreFacilityBookingRequest $request, Facility $facility): RedirectResponse
    {
        abort_unless($facility->is_active, 404);

        $validated = $request->validated();
        $endDate = $validated['end_date'] ?? $validated['reservation_date'];
        $billingType = $validated['billing_type'] ?? ($facility->rate_type ?? 'hourly');

        $durationHours = $this->bookingService->computeDurationHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $startDateCarbon = Carbon::parse($validated['reservation_date']);
        $endDateCarbon = Carbon::parse($endDate);
        $totalDays = max(1, $startDateCarbon->diffInDays($endDateCarbon) + 1);

        $estimatedAmount = $this->bookingService->calculateAmount(
            $facility,
            $validated['start_time'],
            $validated['end_time'],
            $billingType,
            $validated['reservation_date'],
            $endDate
        );

        try {
            $reservation = $this->bookingService->createReservation([
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
                'estimated_amount' => $estimatedAmount,
                'status' => 'pending',
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
            ], $facility, ['approved']);
        } catch (FacilityReservationConflictException $e) {
            return back()->withErrors([
                'reservation_date' => 'The selected date/time slot already has an approved reservation. Please choose a different slot.',
            ])->withInput();
        }

        return redirect()->route('facilities.confirmation', $reservation);
    }

    public function showSet(FacilitySet $facilitySet): View
    {
        abort_unless($facilitySet->is_active, 404);
        $facilitySet->load('facilities');

        $facilityIds = $facilitySet->facilities->pluck('facility_id')->all();

        $approvedReservations = FacilityReservation::whereIn('status', ['approved', 'active'])
            ->where(function ($facQ) use ($facilityIds) {
                $facQ->whereIn('facility_id', $facilityIds)
                    ->orWhereHas('reservedFacilities', function ($sub) use ($facilityIds) {
                        $sub->whereIn('facilities.facility_id', $facilityIds);
                    });
            })
            ->where('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get(['reservation_date', 'start_time', 'end_time']);

        return view('facilities.show_set', compact('facilitySet', 'approvedReservations'));
    }

    public function bookSetForm(FacilitySet $facilitySet): View
    {
        abort_unless($facilitySet->is_active, 404);
        $facilitySet->load('facilities');

        $termsContent = SystemSetting::get('facility_terms_content', 'No terms configured yet.');

        return view('facilities.book_set', compact('facilitySet', 'termsContent'));
    }

    public function submitSetBooking(StoreFacilityBookingRequest $request, FacilitySet $facilitySet): RedirectResponse
    {
        abort_unless($facilitySet->is_active, 404);
        $facilitySet->load('facilities');

        $validated = $request->validated();
        $endDate = $validated['end_date'] ?? $validated['reservation_date'];
        $billingType = $validated['billing_type'] ?? ($facilitySet->rate_type ?? 'hourly');

        $durationHours = $this->bookingService->computeDurationHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $startDateCarbon = Carbon::parse($validated['reservation_date']);
        $endDateCarbon = Carbon::parse($endDate);
        $totalDays = max(1, $startDateCarbon->diffInDays($endDateCarbon) + 1);

        $estimatedAmount = $this->bookingService->calculateAmount(
            $facilitySet,
            $validated['start_time'],
            $validated['end_time'],
            $billingType,
            $validated['reservation_date'],
            $endDate
        );

        try {
            $reservation = $this->bookingService->createReservation([
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
                'estimated_amount' => $estimatedAmount,
                'status' => 'pending',
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
            ], $facilitySet, ['approved']);
        } catch (FacilityReservationConflictException $e) {
            return back()->withErrors([
                'reservation_date' => 'This facility set is unavailable for the selected date and time because one or more included facilities have already been reserved. Please choose another date or time.',
            ])->withInput();
        }

        return redirect()->route('facilities.confirmation', $reservation);
    }

    public function confirmation(FacilityReservation $reservation): View
    {
        $reservation->load(['facility', 'facilitySet', 'reservedFacilities']);

        return view('facilities.confirmation', compact('reservation'));
    }

    public function terms(): View
    {
        $termsContent = SystemSetting::get('facility_terms_content', 'No terms configured yet.');

        return view('facilities.terms', compact('termsContent'));
    }
}
