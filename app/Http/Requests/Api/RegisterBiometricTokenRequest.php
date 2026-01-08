<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterBiometricTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'biometric_token' => ['required', 'string', 'min:32', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }
}
