<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageUploadService
{
    public function __construct(
        private Base64ImageService $base64Service
    ) {
    }

    /**
     * Upload image from base64 string or UploadedFile
     *
     * @param HasMedia $model
     * @param string|UploadedFile $image
     * @param string $collectionName
     * @param string|null $name
     * @return Media
     * @throws \Exception
     */
    public function uploadImage(
        HasMedia $model,
        string|UploadedFile $image,
        string $collectionName,
        ?string $name = null
    ): Media {
        // Handle base64 string
        if (is_string($image)) {
            $image = $this->base64Service->decodeBase64Image($image, $collectionName);
        }

        // Ensure it's an UploadedFile
        if (!($image instanceof UploadedFile)) {
            throw new \Exception('Invalid image format. Expected base64 string or UploadedFile.');
        }

        // Upload to media collection
        $tempPath = $image->getPathname();
        $media = $model->addMedia($tempPath)
            ->usingName($name ?? $collectionName . '_' . time())
            ->usingFileName($image->getClientOriginalName())
            ->toMediaCollection($collectionName);

        // Clean up temp file if it was created from base64
        // Spatie copies the file, so we can safely delete the temp file
        if ($tempPath && str_starts_with($tempPath, sys_get_temp_dir()) && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        return $media;
    }

    /**
     * Upload image from request (handles both multipart and base64)
     *
     * @param HasMedia $model
     * @param mixed $requestData Can be UploadedFile, base64 string, or array with 'photo' key
     * @param string $collectionName
     * @param string|null $name
     * @return Media
     * @throws \Exception
     */
    public function uploadFromRequest(
        HasMedia $model,
        mixed $requestData,
        string $collectionName,
        ?string $name = null
    ): Media {
        // Handle array with 'photo' key (base64 in JSON)
        if (is_array($requestData) && isset($requestData['photo'])) {
            return $this->uploadImage($model, $requestData['photo'], $collectionName, $name);
        }

        // Handle direct base64 string
        if (is_string($requestData) && !empty($requestData)) {
            return $this->uploadImage($model, $requestData, $collectionName, $name);
        }

        // Handle UploadedFile
        if ($requestData instanceof UploadedFile) {
            return $this->uploadImage($model, $requestData, $collectionName, $name);
        }

        throw new \Exception('Invalid image data format');
    }

    /**
     * Get image URLs from media collection
     *
     * @param HasMedia $model
     * @param string $collectionName
     * @param bool $asBase64
     * @return array
     */
    public function getImageUrls(HasMedia $model, string $collectionName, bool $asBase64 = false): array
    {
        $mediaItems = $model->getMedia($collectionName);
        
        return $mediaItems->map(function (Media $media) use ($asBase64) {
            if ($asBase64) {
                $path = $media->getPath();
                $imageData = file_get_contents($path);
                $base64 = base64_encode($imageData);
                $mimeType = $media->mime_type;
                return "data:{$mimeType};base64,{$base64}";
            }
            
            return $media->getUrl();
        })->toArray();
    }
}

