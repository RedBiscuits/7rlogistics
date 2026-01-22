<?php

namespace App\Http\Resources;

use App\Services\ImageUploadService;
use App\Services\StatusMappingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusMapping = app(StatusMappingService::class);
        $imageService = app(ImageUploadService::class);

        // Build pickup address - use from_location if available, otherwise build from area/city
        $pickupAddress = null;
        $pickupLat = null;
        $pickupLng = null;
        
        if ($this->fromArea) {
            // Build from area and optionally city
            $pickupAddress = $this->fromArea->name;
            if ($this->fromCity) {
                $pickupAddress .= ', ' . $this->fromCity->name;
            }
        }
        
        // Get coordinates from area if available
        if ($this->fromArea) {
            $pickupLat = isset($this->fromArea->latitude) ? (float) $this->fromArea->latitude : null;
            $pickupLng = isset($this->fromArea->longitude) ? (float) $this->fromArea->longitude : null;
        }

        // Build delivery address - use to_location if available, otherwise receiver_address
        $deliveryAddress = $this->receiver_address;
        $deliveryLat = $this->latitude;
        $deliveryLng = $this->longitude;

        // Get photos from media collections
        $pickupPhotos = $imageService->getImageUrls($this->resource, 'pickup_photos', false);
        $deliveryPhotos = $imageService->getImageUrls($this->resource, 'delivery_photos', false);
        $tollPhotos = $imageService->getImageUrls($this->resource, 'toll_photos', false);

        // Get signature (from media or text field if exists)
        $signature = null;
        $signatureMedia = $this->resource->getFirstMedia('signature');
        if ($signatureMedia) {
            $signature = $signatureMedia->getUrl();
        } elseif (isset($this->signature)) {
            $signature = $this->signature;
        }

        // Build requestedVehicle: "truckType - plate_number"
        $requestedVehicle = null;
        if ($this->truck) {
            $truckTypeName = $this->truck->truckType?->name ?? '';
            $plateNumber = $this->truck->plate_number ?? '';
            if ($truckTypeName || $plateNumber) {
                $requestedVehicle = trim($truckTypeName . ' - ' . $plateNumber);
            }
        }

        return [
            'id' => (string) $this->id,
            'trackingNumber' => $this->awb ?? $this->tracking_number ?? (string) $this->id,
            'status' => $statusMapping->toMobileApp($this->status),
            'customerName' => $this->receiver_name,
            'customerPhone' => $this->receiver_phone,
            'pickupAddress' => $pickupAddress,
            'pickupLat' => $pickupLat,
            'pickupLng' => $pickupLng,
            'deliveryAddress' => $deliveryAddress,
            'deliveryLat' => $deliveryLat,
            'deliveryLng' => $deliveryLng,
            'fromLocation' => $this->from_location,
            'toLocation' => $this->to_location,
            'requestedVehicle' => $requestedVehicle,
            'specialInstructions' => $this->instructions ?? $this->notes ?? $this->description ?? null,
            'estimatedDelivery' => $this->pickup_date?->format('Y-m-d\TH:i:s\Z'),
            'createdAt' => $this->created_at->format('Y-m-d\TH:i:s\Z'),
            'updatedAt' => $this->updated_at->format('Y-m-d\TH:i:s\Z'),
            'pickupPhotos' => $pickupPhotos,
            'deliveryPhotos' => $deliveryPhotos,
            'tollPhotos' => $tollPhotos,
            'signature' => $signature,
            // Photo requirements info
            'photoRequirements' => [
                'pickup' => [
                    'count' => $this->resource->getPickupPhotosCount(),
                    'required' => 2,
                    'fulfilled' => $this->resource->hasMinimumPickupPhotos(),
                ],
                'delivery' => [
                    'count' => $this->resource->getDeliveryPhotosCount(),
                    'required' => 2,
                    'fulfilled' => $this->resource->hasMinimumDeliveryPhotos(),
                ],
                'signature' => [
                    'required' => true,
                    'fulfilled' => $this->resource->hasSignature(),
                ],
            ],
        ];
    }
}
