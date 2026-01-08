<?php

namespace App\Services;

use App\Enums\ShipmentStatus;

class StatusMappingService
{
    /**
     * Map internal enum to mobile app string value
     */
    public function toMobileApp(ShipmentStatus $status): string
    {
        return $status->toMobileAppValue();
    }

    /**
     * Map mobile app string value to internal enum
     */
    public function fromMobileApp(string $value): ShipmentStatus
    {
        return ShipmentStatus::fromMobileAppValue($value);
    }

    /**
     * Get all valid mobile app status values
     */
    public function getValidMobileAppStatuses(): array
    {
        return [
            'pending',
            'awaiting_acceptance',
            'accepted',
            'on_way',
            'awaiting_loading',
            'received',
            'picked_up', // Backward compatibility
            'in_transit',
            'delivered',
            'exception',
        ];
    }
}






