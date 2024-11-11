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
            self::DRIVER_RECEIVED => 'Driver Received',
            self::ON_ITS_WAY => "On It's Way",
            self::DELIVERED => 'Delivered', // Completed
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'CANCELLED',
            self::PENDING => 'PENDING',
            self::APPROVED => 'success',
            self::ASSIGNEDMISSION => 'ASSIGNEDMISSION',
            self::DRIVER_RECEIVED => 'DRIVER_RECEIVED',
            self::ON_ITS_WAY => 'ON_ITS_WAY',
            self::DELIVERED => 'success',
        };
    }
}
