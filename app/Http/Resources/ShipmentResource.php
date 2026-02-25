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

        // Resolve stops if loaded
        $pickupStops = $this->whenLoaded('stops', function () {
            return $this->stops->where('type', 'pickup')->sortBy('sequence')->values();
        });
        $deliveryStops = $this->whenLoaded('stops', function () {
            return $this->stops->where('type', 'delivery')->sortBy('sequence')->values();
        });

        $firstPickup = $pickupStops instanceof \Illuminate\Support\Collection ? $pickupStops->first() : null;
        $firstDelivery = $deliveryStops instanceof \Illuminate\Support\Collection ? $deliveryStops->first() : null;

        // Build pickup address -- prefer stop name, fall back to legacy columns
        $pickupAddress = null;
        if ($firstPickup) {
            $pickupAddress = $firstPickup->name ?: $firstPickup->location;
        } elseif ($this->fromArea) {
            $pickupAddress = $this->fromArea->name;
            if ($this->fromCity) {
                $pickupAddress .= ', ' . $this->fromCity->name;
            }
        }

        // Build delivery address -- prefer stop name, fall back to legacy columns
        $deliveryAddress = null;
        if ($firstDelivery) {
            $deliveryAddress = $firstDelivery->name ?: $firstDelivery->location;
        } else {
            $deliveryAddress = $this->receiver_address;
        }

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
            'trackingNumber' => (string) $this->id,
            'status' => $statusMapping->toMobileApp($this->status),
            'customerName' => $this->receiver_name,
            'customerPhone' => $this->receiver_phone,
            'pickupAddress' => $pickupAddress,
            'deliveryAddress' => $deliveryAddress,
            'fromLocation' => $this->from_location,
            'toLocation' => $this->to_location,
            'areaFrom' => $this->fromArea?->name,
            'areaTo' => $this->toArea?->name,
            'requestedVehicle' => $requestedVehicle,
            'carType' => $this->truck?->truckType?->name,
            'specialInstructions' => $this->instructions ?? $this->notes ?? $this->description ?? null,
            'estimatedDelivery' => $this->pickup_date?->format('Y-m-d\TH:i:s\Z'),
            'createdAt' => $this->created_at->format('Y-m-d\TH:i:s\Z'),
            'updatedAt' => $this->updated_at->format('Y-m-d\TH:i:s\Z'),
            'pickupPhotos' => $pickupPhotos,
            'deliveryPhotos' => $deliveryPhotos,
            'tollPhotos' => $tollPhotos,
            'signature' => $signature,
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
            'stops' => $this->when($this->relationLoaded('stops') && $this->stops->isNotEmpty(), function () use ($pickupStops, $deliveryStops) {
                return [
                    'pickup' => ShipmentStopResource::collection($pickupStops),
                    'delivery' => ShipmentStopResource::collection($deliveryStops),
                ];
            }),
        ];
    }
}
