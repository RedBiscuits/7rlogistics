<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\DriverResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\ImageUploadService;
use App\Services\LicenseValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private LicenseValidationService $licenseService,
        private ImageUploadService $imageService
    ) {
    }

    public function profile(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        return $this->successResponse(new DriverResource($driver));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Update name (split firstName/lastName if provided)
        if ($request->has('firstName') || $request->has('lastName')) {
            $firstName = $request->input('firstName', '');
            $lastName = $request->input('lastName', '');
            $driver->name = trim($firstName . ' ' . $lastName);
        }

        // Update phone
        if ($request->has('phone')) {
            $driver->contact_number = $request->input('phone');
        }

        // Update avatar (base64 or URL)
        if ($request->has('avatar')) {
            $avatar = $request->input('avatar');
            // If it's a base64 string, upload it
            if (str_starts_with($avatar, 'data:image') || (strlen($avatar) > 100 && base64_decode($avatar, true) !== false)) {
                try {
                    $this->imageService->uploadFromRequest($driver, $avatar, 'profile_photo');
                } catch (\Exception $e) {
                    // If upload fails, continue without avatar update
                }
            }
            // If it's a URL, we might want to store it differently or fetch it
        }

        $driver->save();
        $driver->refresh();

        return $this->successResponse(new DriverResource($driver));
    }

    public function licenseStatus(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $status = $this->licenseService->checkLicenseStatus($driver);

        return $this->successResponse($status);
    }

    public function documents(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $licensePhotos = $driver->getLicensePhotos();
        $idPhoto = $driver->getFirstMedia('id_photo');
        $profilePhoto = $driver->getFirstMedia('profile_photo');

        return $this->successResponse([
            'license_front' => $licensePhotos['front'] ? [
                'url' => $licensePhotos['front']->getUrl(),
                'thumb' => $licensePhotos['front']->getUrl('thumb'),
            ] : null,
            'license_back' => $licensePhotos['back'] ? [
                'url' => $licensePhotos['back']->getUrl(),
                'thumb' => $licensePhotos['back']->getUrl('thumb'),
            ] : null,
            'id_photo' => $idPhoto ? [
                'url' => $idPhoto->getUrl(),
                'thumb' => $idPhoto->getUrl('thumb'),
            ] : null,
            'profile_photo' => $profilePhoto ? [
                'url' => $profilePhoto->getUrl(),
                'thumb' => $profilePhoto->getUrl('thumb'),
            ] : null,
        ]);
    }
}
