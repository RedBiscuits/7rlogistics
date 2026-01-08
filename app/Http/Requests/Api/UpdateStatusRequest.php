<?php

namespace App\Http\Requests\Api;

use App\Services\StatusMappingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusMapping = app(StatusMappingService::class);
        $validStatuses = $statusMapping->getValidMobileAppStatuses();

        return [
            'status' => ['required', 'string', Rule::in($validStatuses)],
        ];
    }
}
