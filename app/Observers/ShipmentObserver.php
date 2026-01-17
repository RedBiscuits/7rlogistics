<?php

namespace App\Observers;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class ShipmentObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    /**
     * Handle the Shipment "created" event.
     * Notify driver when shipment is created with driver assigned and status is ASSIGNEDMISSION
     */
    public function created(Shipment $shipment): void
    {
        Log::info('ShipmentObserver::created triggered', [
            'shipment_id' => $shipment->id,
            'driver_id' => $shipment->driver_id,
            'status' => $this->getStatusValue($shipment),
        ]);

        $this->notifyDriverIfAssigned($shipment, 'created');
    }

    /**
     * Handle the Shipment "updated" event.
     * Notify driver when:
     * - Driver is assigned/changed and status is ASSIGNEDMISSION
     * - Status changes to ASSIGNEDMISSION and driver exists
     * - Driver is changed to a different driver while status is ASSIGNEDMISSION
     */
    public function updated(Shipment $shipment): void
    {
        $driverChanged = $shipment->wasChanged('driver_id');
        $statusChanged = $shipment->wasChanged('status');
        $currentDriverId = $shipment->driver_id;
        $originalDriverId = $shipment->getOriginal('driver_id');
        $statusValue = $this->getStatusValue($shipment);

        Log::info('ShipmentObserver::updated triggered', [
            'shipment_id' => $shipment->id,
            'driver_changed' => $driverChanged,
            'status_changed' => $statusChanged,
            'current_driver_id' => $currentDriverId,
            'original_driver_id' => $originalDriverId,
            'status' => $statusValue,
            'status_is_assigned_mission' => $statusValue === ShipmentStatus::ASSIGNEDMISSION->value,
        ]);

        // Only proceed if we have a driver and status is ASSIGNEDMISSION or APPROVED
        if (!$currentDriverId) {
            Log::debug('ShipmentObserver: No driver assigned, skipping notification');
            return;
        }

        if (!$this->isNotifiableStatus($statusValue)) {
            Log::debug('ShipmentObserver: Status is not notifiable, skipping notification', [
                'status' => $statusValue,
                'notifiable_statuses' => [ShipmentStatus::ASSIGNEDMISSION->value, ShipmentStatus::APPROVED->value],
            ]);
            return;
        }

        // Notify if:
        // 1. Driver was just assigned (from null to a driver)
        // 2. Driver was changed to a different driver
        // 3. Status just changed to ASSIGNEDMISSION and driver exists
        $shouldNotify = false;
        $trigger = '';

        if ($driverChanged) {
            // Driver changed - either newly assigned or changed to different driver
            if ($originalDriverId === null) {
                $shouldNotify = true;
                $trigger = 'driver_assigned';
            } else {
                // Driver changed to a different driver - notify the NEW driver
                $shouldNotify = true;
                $trigger = 'driver_changed';
            }
        } elseif ($statusChanged && $this->isNotifiableStatus($statusValue)) {
            // Status changed to a notifiable status (ASSIGNEDMISSION or APPROVED) with existing driver
            $shouldNotify = true;
            $trigger = 'status_changed_to_notifiable';
        }

        if ($shouldNotify) {
            $this->sendNotification($shipment, $trigger);
        }
    }

    /**
     * Check if status is one that should trigger driver notification
     */
    private function isNotifiableStatus(int $statusValue): bool
    {
        return in_array($statusValue, [
            ShipmentStatus::ASSIGNEDMISSION->value,
            ShipmentStatus::APPROVED->value,
        ]);
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

    /**
     * Notify driver if shipment is assigned with a notifiable status (ASSIGNEDMISSION or APPROVED)
     */
    private function notifyDriverIfAssigned(Shipment $shipment, string $trigger): void
    {
        $statusValue = $this->getStatusValue($shipment);

        // Must have a driver
        if (!$shipment->driver_id) {
            Log::debug('ShipmentObserver: No driver_id, skipping notification');
            return;
        }

        // Status must be ASSIGNEDMISSION or APPROVED
        if (!$this->isNotifiableStatus($statusValue)) {
            Log::debug('ShipmentObserver: Status is not notifiable, skipping notification', [
                'shipment_id' => $shipment->id,
                'driver_id' => $shipment->driver_id,
                'status' => $statusValue,
                'trigger' => $trigger,
            ]);
            return;
        }

        $this->sendNotification($shipment, $trigger);
    }

    /**
     * Send the notification to the driver
     */
    private function sendNotification(Shipment $shipment, string $trigger): void
    {
        // Load driver relationship if not loaded
        if (!$shipment->relationLoaded('driver')) {
            $shipment->load('driver');
        }

        if (!$shipment->driver) {
            Log::error('ShipmentObserver: Driver not found', [
                'shipment_id' => $shipment->id,
                'driver_id' => $shipment->driver_id,
            ]);
            return;
        }

        Log::info('ShipmentObserver: Sending notification to driver', [
            'shipment_id' => $shipment->id,
            'driver_id' => $shipment->driver_id,
            'driver_name' => $shipment->driver->name,
            'trigger' => $trigger,
        ]);

        try {
            $this->notificationService->notifyShipmentAssigned(
                $shipment->driver,
                $shipment
            );
            Log::info('ShipmentObserver: Notification sent successfully', [
                'shipment_id' => $shipment->id,
                'driver_id' => $shipment->driver_id,
            ]);
        } catch (\Exception $e) {
            Log::error('ShipmentObserver: Failed to send notification', [
                'shipment_id' => $shipment->id,
                'driver_id' => $shipment->driver_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get status value regardless of whether it's an enum or int
     */
    private function getStatusValue(Shipment $shipment): int
    {
        return $shipment->status instanceof ShipmentStatus 
            ? $shipment->status->value 
            : (int) $shipment->status;
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

