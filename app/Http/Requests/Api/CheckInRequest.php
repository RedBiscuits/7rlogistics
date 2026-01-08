<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => ['required_without:latitude', 'numeric', 'between:-90,90'],
            'lng' => ['required_without:longitude', 'numeric', 'between:-180,180'],
            'latitude' => ['required_without:lat', 'numeric', 'between:-90,90'], // Backward compatibility
            'longitude' => ['required_without:lng', 'numeric', 'between:-180,180'], // Backward compatibility
            'location_id' => ['nullable', 'integer', Rule::exists('attendance_locations', 'id')],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map lat/lng to latitude/longitude for backward compatibility
        if ($this->has('lat') && !$this->has('latitude')) {
            $this->merge([
                'latitude' => $this->input('lat'),
            ]);
        }
        if ($this->has('lng') && !$this->has('longitude')) {
            $this->merge([
                'longitude' => $this->input('lng'),
            ]);
        }
    }
}
