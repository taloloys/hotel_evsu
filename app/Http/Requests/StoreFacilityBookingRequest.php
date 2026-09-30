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
            'booker_email' => ['required', 'email:rfc,dns', 'max:255'],
            'booker_contact' => ['required', 'string', 'max:30'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
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
