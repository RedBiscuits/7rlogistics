<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClaimStatus: int implements HasColor, HasLabel
{
    case PENDING = 0;
    case COLLECTED = 1;
    case CANCELLED = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COLLECTED => 'Approved',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PENDING => 'PENDING',
            self::COLLECTED => 'success',
            self::CANCELLED => 'CANCELLED',
        };
    }
}
