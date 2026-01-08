<?php

namespace App\Http\Requests\Api;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_token' => ['required', 'string'],
            'device_type' => ['required', 'integer', Rule::in(array_map(fn($case) => $case->value, DeviceType::cases()))],
            'app_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}
