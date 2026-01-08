<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => ['required_without:driver_code', 'string'],
            'driver_code' => ['required_without:username', 'string'], // Backward compatibility
            'password' => ['required_without:biometric_token', 'string'],
            'biometric_token' => ['required_without:password', 'string', 'min:32'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map username to driver_code for backward compatibility
        if ($this->has('username') && !$this->has('driver_code')) {
            $this->merge([
                'driver_code' => $this->input('username'),
            ]);
        }
    }
}
