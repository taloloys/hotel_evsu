<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public route — no auth required
    }

    public function rules(): array
    {
        return [
            'booker_name' => ['required', 'string', 'max:255'],
            'booker_email' => ['required', app()->environment('testing') ? 'email:rfc' : 'email:rfc,dns', 'max:255'],
            'booker_contact' => ['required', 'string', 'max:30'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'event_details' => ['nullable', 'string', 'max:1000'],
            'billing_type' => ['nullable', 'in:hourly,daily'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:reservation_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'terms_accepted' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.required' => 'You must accept the Terms and Conditions before submitting.',
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions before submitting.',
            'end_time.after' => 'End time must be after the start time.',
            'reservation_date.after_or_equal' => 'Reservation date must be today or a future date.',
        ];
    }
}
