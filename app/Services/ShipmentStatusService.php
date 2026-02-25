<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Illuminate\Http\UploadedFile;

class ShipmentStatusService
{
    public function acknowledgeShipment(Shipment $shipment): Shipment
    {
        if (!in_array($shipment->status, [ShipmentStatus::ASSIGNEDMISSION, ShipmentStatus::APPROVED])) {
            throw new \InvalidArgumentException('Shipment must be in ASSIGNED_MISSION or APPROVED status to be acknowledged.');
        }

        $shipment->update([
            'status' => ShipmentStatus::DRIVER_ACKNOWLEDGED,
            'driver_acknowledged_at' => now(),
        ]);

        return $shipment->fresh();
    }

    public function updateStatus(Shipment $shipment, ShipmentStatus $newStatus, ?array $proofs = null): Shipment
    {
        // Validate status transition
        $this->validateStatusTransition($shipment, $newStatus);

        // Record timestamp
        $this->recordStatusTimestamp($shipment, $newStatus);

        // Update status
        $shipment->update([
            'status' => $newStatus,
        ]);

        return $shipment->fresh();
    }

    /**
     * Validate photo requirements for status transitions
     */
    public function validatePhotoRequirements(Shipment $shipment, ShipmentStatus $newStatus): void
    {
        // Transitioning to DRIVER_RECEIVED requires minimum 2 pickup photos
        if ($newStatus === ShipmentStatus::DRIVER_RECEIVED) {
            if (!$shipment->hasMinimumPickupPhotos()) {
                $currentCount = $shipment->getPickupPhotosCount();
                throw new \InvalidArgumentException(
                    "Minimum 2 pickup photos required before marking as received. Currently have {$currentCount} photo(s). Please upload pickup photos first."
                );
            }
        }

        // Transitioning to DELIVERED requires minimum 2 delivery photos and a signature
        if ($newStatus === ShipmentStatus::DELIVERED) {
            if (!$shipment->hasMinimumDeliveryPhotos()) {
                $currentCount = $shipment->getDeliveryPhotosCount();
                throw new \InvalidArgumentException(
                    "Minimum 2 delivery photos required before marking as delivered. Currently have {$currentCount} photo(s). Please upload delivery photos first."
                );
            }

            if (!$shipment->hasSignature()) {
                throw new \InvalidArgumentException(
                    "Customer signature is required before marking as delivered. Please capture the signature first."
                );
            }
        }
    }

    public function validateStatusTransition(Shipment $shipment, ShipmentStatus $newStatus): void
    {
        if (!$shipment->status->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Invalid status transition from {$shipment->status->getLabel()} to {$newStatus->getLabel()}"
            );
        }
    }

    public function validateProofs(Shipment $shipment, ShipmentStatus $status, array $proofs): void
    {
        if ($status === ShipmentStatus::DELIVERED) {
            if (count($proofs) < 2) {
                throw new \InvalidArgumentException('Minimum 2 images required for DELIVERED status.');
            }

            // Validate each file
            foreach ($proofs as $proof) {
                if (!($proof instanceof UploadedFile)) {
                    throw new \InvalidArgumentException('Invalid proof file provided.');
                }

                // Check file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($proof->getMimeType(), $allowedMimes)) {
                    throw new \InvalidArgumentException('Proof files must be JPEG or PNG.');
                }

                // Check file size (5MB max)
                if ($proof->getSize() > 5 * 1024 * 1024) {
                    throw new \InvalidArgumentException('Proof files must be less than 5MB.');
                }
            }
        }
    }

    public function recordStatusTimestamp(Shipment $shipment, ShipmentStatus $status): void
    {
        $timestampField = match ($status) {
            ShipmentStatus::DRIVER_ACKNOWLEDGED => 'driver_acknowledged_at',
            ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT => 'driver_on_way_to_collect_at',
            ShipmentStatus::AWAITING_COLLECTION => 'awaiting_collection_at',
            default => null,
        };

        if ($timestampField) {
            $shipment->update([
                $timestampField => now(),
            ]);
        }
    }
}








