<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityBookingRequest;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\SystemSetting;
use App\Services\FacilityBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicFacilityController extends Controller
{
    public function __construct(private readonly FacilityBookingService $bookingService) {}

    public function show(Facility $facility): View
    {
        abort_unless($facility->is_active, 404);

        $approvedReservations = $facility->reservations()
            ->where('status', 'approved')
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

        // Server-side conflict check (approved reservations only for public)
        if ($this->bookingService->hasConflict(
            $facility->facility_id,
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return back()->withErrors([
                'reservation_date' => 'The selected date and time is already reserved. Please choose a different slot.',
            ])->withInput();
        }

        $durationHours = $this->bookingService->computeDurationHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $estimatedAmount = $this->bookingService->calculateAmount(
            $facility,
            $validated['start_time'],
            $validated['end_time']
        );

        $reservation = FacilityReservation::create([
            'facility_id' => $facility->facility_id,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_contact' => $validated['booker_contact'],
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
            'duration_hours' => $durationHours,
            'estimated_amount' => $estimatedAmount,
            'status' => 'pending',
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        return redirect()->route('facilities.confirmation', $reservation);
    }

    public function confirmation(FacilityReservation $reservation): View
    {
        $reservation->load('facility');

        return view('facilities.confirmation', compact('reservation'));
    }

    public function terms(): View
    {
        $termsContent = SystemSetting::get('facility_terms_content', 'No terms configured yet.');

        return view('facilities.terms', compact('termsContent'));
    }
}
