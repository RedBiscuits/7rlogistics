<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Base64ImageService
{
    /**
     * Decode base64 image string and create UploadedFile instance
     *
     * @param string $base64String
     * @param string $collectionName
     * @return UploadedFile
     * @throws \Exception
     */
    public function decodeBase64Image(string $base64String, string $collectionName = 'default'): UploadedFile
    {
        // Remove data URL prefix if present (e.g., "data:image/png;base64,")
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $imageType = $matches[1];
            $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
        } else {
            // Try to detect image type from base64 string
            $imageType = 'png'; // Default
        }

        // Decode base64 string
        $imageData = base64_decode($base64String, true);
        
        if ($imageData === false) {
            throw new \Exception('Invalid base64 image string');
        }

        // Validate it's actually an image
        $imageInfo = @getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            throw new \Exception('Invalid image data');
        }

        // Determine file extension
        $extension = match ($imageInfo[2]) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
            default => $imageType ?? 'jpg',
        };

        // Create temporary file
        $tempPath = sys_get_temp_dir() . '/' . Str::uuid() . '.' . $extension;
        file_put_contents($tempPath, $imageData);

        // Create UploadedFile instance
        $uploadedFile = new UploadedFile(
            $tempPath,
            $collectionName . '_' . time() . '.' . $extension,
            mime_content_type($tempPath),
            null,
            true // test mode
        );

        return $uploadedFile;
    }

    /**
     * Validate base64 image string
     *
     * @param string $base64String
     * @return bool
     */
    public function isValidBase64Image(string $base64String): bool
    {
        try {
            $this->decodeBase64Image($base64String);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}






