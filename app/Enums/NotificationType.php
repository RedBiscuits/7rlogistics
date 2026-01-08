<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum NotificationType: int implements HasLabel, HasColor
{
    case SHIPMENT_ASSIGNED = 0;
    case LICENSE_EXPIRY = 1;
    case GENERAL = 2;
    case PICKUP_REMINDER = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SHIPMENT_ASSIGNED => 'Shipment Assigned',
            self::LICENSE_EXPIRY => 'License Expiry',
            self::GENERAL => 'General',
            self::PICKUP_REMINDER => 'Pickup Reminder',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SHIPMENT_ASSIGNED => 'info',
            self::LICENSE_EXPIRY => 'warning',
            self::GENERAL => 'gray',
            self::PICKUP_REMINDER => 'success',
        };
    }
}








