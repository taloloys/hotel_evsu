<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-facilities') || $this->user()?->can('manage-landing-page');
    }

    public function rules(): array
    {
        return [
            'facility_type' => ['required', 'in:single,set'],
            'name' => ['required', 'string', 'max:255'],
            'prefix_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'rate_type' => ['required', 'in:hourly,daily,both'],
            'is_active' => ['boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_paths' => ['nullable', 'string'],
            'member_facilities' => ['nullable', 'array'],
            'member_facilities.*' => ['integer', 'exists:facilities,facility_id'],
        ];
    }
}
