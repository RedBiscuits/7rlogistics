<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required_without:image', 'string'], // Base64 string
            'image' => ['required_without:photo', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // Multipart file
        ];
    }

    /**
     * Get the photo data (either base64 string or uploaded file)
     */
    public function getPhoto()
    {
        if ($this->has('photo')) {
            return $this->input('photo');
        }
        
        if ($this->hasFile('image')) {
            return $this->file('image');
        }

        return null;
    }
}






