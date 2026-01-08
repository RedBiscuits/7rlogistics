<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $licensePhotos = $this->getLicensePhotos();
        $profilePhoto = $this->getFirstMedia('profile_photo');

        // Split name into firstName and lastName
        $nameParts = explode(' ', $this->name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Get vehicle info from most recent active shipment or use car_license fields
        $vehicleId = null;
        $vehiclePlate = $this->car_license ?? null;
        $vehicleModel = $this->car_type ?? null;

        // Try to get from most recent active shipment
        $recentShipment = $this->missions()
            ->whereNotNull('truck_id')
            ->with('truck.truckType')
            ->latest()
            ->first();

        if ($recentShipment && $recentShipment->truck) {
            $vehicleId = (string) $recentShipment->truck->id;
            $vehiclePlate = $recentShipment->truck->plate_number ?? $vehiclePlate;
            $vehicleModel = $recentShipment->truck->truckType?->name ?? $vehicleModel;
        }

        return [
            'id' => (string) $this->id,
            'username' => $this->code,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $this->contact_number,
            'avatar' => $profilePhoto ? $profilePhoto->getUrl() : null,
            'licenseNumber' => $this->driving_license_number,
            'licenseExpiry' => $this->driving_license_expire_date?->format('Y-m-d'),
            'licenseType' => null, // Not available in current schema
            'vehicleId' => $vehicleId,
            'vehiclePlate' => $vehiclePlate,
            'vehicleModel' => $vehicleModel,
            'createdAt' => $this->created_at->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
