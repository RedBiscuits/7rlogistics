<?php

namespace App\Http\Controllers\Api;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateStatusRequest;
use App\Http\Requests\Api\UploadPhotoRequest;
use App\Http\Requests\Api\UploadProofsRequest;
use App\Http\Requests\Api\UploadSignatureRequest;
use App\Http\Resources\ShipmentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Shipment;
use App\Services\ImageUploadService;
use App\Services\LicenseValidationService;
use App\Services\ShipmentStatusService;
use App\Services\StatusMappingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ShipmentStatusService $statusService,
        private LicenseValidationService $licenseService,
        private StatusMappingService $statusMapping,
        private ImageUploadService $imageService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $query = Shipment::where('driver_id', $driver->id)
            ->where('created_at', '>=', now()->subDays(40))
            ->with([
                'driver',
                'truck.truckType',
                'fromArea',
                'toArea',
                'fromCity',
                'toCity',
                'company',
                'packages',
                'stops',
            ]);

        // Filter by status (mobile app string values)
        if ($request->has('status')) {
            $statusString = $request->status;
            try {
                $statusEnum = $this->statusMapping->fromMobileApp($statusString);
                
                // For 'awaiting_acceptance', include both ASSIGNEDMISSION and APPROVED
                if ($statusEnum === ShipmentStatus::ASSIGNEDMISSION) {
                    $query->whereIn('status', [
                        ShipmentStatus::ASSIGNEDMISSION,
                        ShipmentStatus::APPROVED,
                    ]);
                } else {
                    $query->where('status', $statusEnum);
                }
            } catch (\InvalidArgumentException $e) {
                // Invalid status, return empty result
                $query->whereRaw('1 = 0');
            }
        }

        // Pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);
        $shipments = $query->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse(ShipmentResource::collection($shipments->items()));
    }

    public function show(Shipment $shipment, Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        $shipment->load([
            'driver',
            'truck.truckType',
            'fromArea',
            'toArea',
            'fromCity',
            'toCity',
            'company',
            'packages',
            'stops',
        ]);

        return $this->successResponse(new ShipmentResource($shipment));
    }

    public function accept(Shipment $shipment, Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $shipment = $this->statusService->acknowledgeShipment($shipment);
            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function acknowledge(Shipment $shipment, Request $request): JsonResponse
    {
        // Alias for accept (backward compatibility)
        return $this->accept($shipment, $request);
    }

    public function updateStatus(UpdateStatusRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            // Convert mobile app status string to enum
            $newStatus = $this->statusMapping->fromMobileApp($request->status);
            $shipment = $this->statusService->updateStatus(
                $shipment,
                $newStatus,
                $request->file('proofs')
            );

            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function uploadPickupPhoto(UploadPhotoRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $photo = $request->getPhoto();
            $this->imageService->uploadFromRequest($shipment, $photo, 'pickup_photos');
            $shipment->refresh();

            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse(__('api.failed_upload_photo', ['error' => $e->getMessage()]), 500);
        }
    }

    public function uploadDeliveryPhoto(UploadPhotoRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $photo = $request->getPhoto();
            $this->imageService->uploadFromRequest($shipment, $photo, 'delivery_photos');
            $shipment->refresh();

            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse(__('api.failed_upload_photo', ['error' => $e->getMessage()]), 500);
        }
    }

    public function uploadTollPhoto(UploadPhotoRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $photo = $request->getPhoto();
            $this->imageService->uploadFromRequest($shipment, $photo, 'toll_photos');
            $shipment->refresh();

            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse(__('api.failed_upload_photo', ['error' => $e->getMessage()]), 500);
        }
    }

    public function uploadSignature(UploadSignatureRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $signature = $request->input('signature');
            $this->imageService->uploadFromRequest($shipment, $signature, 'signature');
            $shipment->refresh();

            return $this->successResponse(new ShipmentResource($shipment));
        } catch (\Exception $e) {
            return $this->errorResponse(__('api.failed_upload_signature', ['error' => $e->getMessage()]), 500);
        }
    }

    public function countPending(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $count = Shipment::where('driver_id', $driver->id)
            ->whereIn('status', [
                ShipmentStatus::ASSIGNEDMISSION,
                ShipmentStatus::APPROVED,
            ])
            ->count();

        return $this->successResponse($count);
    }

    public function countActive(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $count = Shipment::where('driver_id', $driver->id)
            ->whereIn('status', [
                ShipmentStatus::DRIVER_ACKNOWLEDGED,
                ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT,
                ShipmentStatus::AWAITING_COLLECTION,
                ShipmentStatus::DRIVER_RECEIVED,
                ShipmentStatus::ON_ITS_WAY,
            ])
            ->count();

        return $this->successResponse($count);
    }

    public function countDeliveredToday(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $count = Shipment::where('driver_id', $driver->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereDate('updated_at', today())
            ->count();

        return $this->successResponse($count);
    }

    // Backward compatibility
    public function uploadProofs(UploadProofsRequest $request, Shipment $shipment): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify shipment belongs to driver
        if ($shipment->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        try {
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                $shipment->addMedia($image)
                    ->usingName('delivery_proof_' . ($index + 1))
                    ->toMediaCollection('delivery_proofs');
            }

            return $this->successResponse([
                'message' => __('api.proofs_uploaded'),
                'count' => count($images),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(__('api.failed_upload_proofs', ['error' => $e->getMessage()]), 500);
        }
    }
}
