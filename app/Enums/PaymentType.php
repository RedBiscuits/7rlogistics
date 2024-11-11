<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: int implements HasColor, HasLabel
{
    case CASH = 0;
    case CHEQUE = 1;
    case BANK = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CHEQUE => 'Cheque',
            self::BANK => 'Bank Transfer',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::CASH => 'CANCELLED',
            self::CHEQUE => 'ON_ITS_WAY',
            self::BANK => 'success'
        };
    }
}
