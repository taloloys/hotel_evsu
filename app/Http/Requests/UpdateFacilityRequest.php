<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-landing-page') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'rate' => ['required', 'numeric', 'min:0'],
            'rate_type' => ['required', 'in:hourly,daily'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'image_paths' => ['nullable', 'string'],
        ];
    }
}
