<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentStatus: int implements HasColor, HasLabel
{
    case CANCELLED = 1;
    case PENDING = 0;
    case APPROVED = 6;
    case ASSIGNEDMISSION = 2;
    case DRIVER_ACKNOWLEDGED = 7;
    case DRIVER_ON_WAY_TO_COLLECT = 8;
    case AWAITING_COLLECTION = 9;
    case DRIVER_RECEIVED = 3;
    case ON_ITS_WAY = 4;
    case DELIVERED = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'Cancelled',
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::ASSIGNEDMISSION => 'Assigned Mission',
            self::DRIVER_ACKNOWLEDGED => 'Driver Acknowledged',
            self::DRIVER_ON_WAY_TO_COLLECT => 'On Way to Collect',
            self::AWAITING_COLLECTION => 'Awaiting Collection',
            self::DRIVER_RECEIVED => 'Driver Received',
            self::ON_ITS_WAY => "On It's Way",
            self::DELIVERED => 'Delivered',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'danger',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::ASSIGNEDMISSION => 'info',
            self::DRIVER_ACKNOWLEDGED => 'info',
            self::DRIVER_ON_WAY_TO_COLLECT => 'info',
            self::AWAITING_COLLECTION => 'info',
            self::DRIVER_RECEIVED => 'primary',
            self::ON_ITS_WAY => 'primary',
            self::DELIVERED => 'success',
        };
    }

    /**
     * Get valid next statuses for current status
     */
    public function getValidNextStatuses(): array
    {
        return match ($this) {
            self::ASSIGNEDMISSION => [self::DRIVER_ACKNOWLEDGED],
            self::APPROVED => [self::DRIVER_ACKNOWLEDGED], // APPROVED can also transition to acknowledged
            self::DRIVER_ACKNOWLEDGED => [self::DRIVER_ON_WAY_TO_COLLECT],
            self::DRIVER_ON_WAY_TO_COLLECT => [self::AWAITING_COLLECTION],
            self::AWAITING_COLLECTION => [self::DRIVER_RECEIVED],
            self::DRIVER_RECEIVED => [self::ON_ITS_WAY],
            self::ON_ITS_WAY => [self::DELIVERED],
            default => [],
        };
    }

    /**
     * Check if status transition is valid
     */
    public function canTransitionTo(ShipmentStatus $newStatus): bool
    {
        return in_array($newStatus, $this->getValidNextStatuses());
    }

    /**
     * Convert enum to mobile app string value
     */
    public function toMobileAppValue(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::ASSIGNEDMISSION => 'awaiting_acceptance',
            self::APPROVED => 'awaiting_acceptance', // APPROVED also awaits driver acceptance
            self::DRIVER_ACKNOWLEDGED => 'accepted',
            self::DRIVER_ON_WAY_TO_COLLECT => 'on_way',
            self::AWAITING_COLLECTION => 'awaiting_loading',
            self::DRIVER_RECEIVED => 'received',
            self::ON_ITS_WAY => 'in_transit',
            self::DELIVERED => 'delivered',
            self::CANCELLED => 'exception',
        };
    }

    /**
     * Convert mobile app string value to enum
     */
    public static function fromMobileAppValue(string $value): self
    {
        return match ($value) {
            'pending' => self::PENDING,
            'awaiting_acceptance' => self::ASSIGNEDMISSION,
            'accepted' => self::DRIVER_ACKNOWLEDGED,
            'on_way' => self::DRIVER_ON_WAY_TO_COLLECT,
            'awaiting_loading' => self::AWAITING_COLLECTION,
            'received' => self::DRIVER_RECEIVED,
            'picked_up' => self::DRIVER_RECEIVED, // Backward compatibility
            'in_transit' => self::ON_ITS_WAY,
            'delivered' => self::DELIVERED,
            'exception' => self::CANCELLED,
            default => throw new \InvalidArgumentException("Invalid mobile app status: {$value}"),
        };
    }
}
