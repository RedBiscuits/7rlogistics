<?php

namespace App\Enums;

enum ClaimType: string
{
    case EXTRA_PICKUP = 'extra_pickup';
    case DETENTION = 'detention';
    case OFFICIAL_RECEIPT = 'official_receipt';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::EXTRA_PICKUP => 'Extra Pickup Location',
            self::DETENTION => 'Detention',
            self::OFFICIAL_RECEIPT => 'Official Receipt',
            self::OTHER => 'Other',
        };
    }
} 