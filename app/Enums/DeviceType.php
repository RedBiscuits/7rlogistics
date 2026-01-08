<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DeviceType: int implements HasLabel
{
    case ANDROID = 0;
    case IOS = 1;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ANDROID => 'Android',
            self::IOS => 'iOS',
        };
    }
}








