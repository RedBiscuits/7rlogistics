<?php

namespace App\Observers;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\NotificationService;

class ShipmentObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function updated(Shipment $shipment): void
    {
        // Check if driver was just assigned
        if ($shipment->wasChanged('driver_id') && 
            $shipment->driver_id) {
            
            // Get status value (could be enum or int)
            $statusValue = $shipment->status instanceof ShipmentStatus 
                ? $shipment->status->value 
                : $shipment->status;
            
            if ($statusValue === ShipmentStatus::ASSIGNEDMISSION->value) {
                $this->notificationService->notifyShipmentAssigned(
                    $shipment->driver,
                    $shipment
                );
            }
        }
    }

    public function updating(Shipment $shipment): void
    {
        // Record status change timestamps
        if ($shipment->isDirty('status')) {
            $newStatusValue = $shipment->getAttribute('status');
            if (is_int($newStatusValue)) {
                $newStatus = ShipmentStatus::from($newStatusValue);
                $this->recordStatusTimestamp($shipment, $newStatus);
            }
        }
    }

    private function recordStatusTimestamp(Shipment $shipment, ShipmentStatus $status): void
    {
        $timestampField = match ($status) {
            ShipmentStatus::DRIVER_ACKNOWLEDGED => 'driver_acknowledged_at',
            ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT => 'driver_on_way_to_collect_at',
            ShipmentStatus::AWAITING_COLLECTION => 'awaiting_collection_at',
            default => null,
        };

        if ($timestampField && !$shipment->$timestampField) {
            $shipment->$timestampField = now();
        }
    }
}

