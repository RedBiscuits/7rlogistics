<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'], // Backward compatibility
            'longitude' => ['nullable', 'numeric', 'between:-180,180'], // Backward compatibility
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
