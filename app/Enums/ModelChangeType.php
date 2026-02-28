<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ModelChangeType: string implements HasLabel, HasColor
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::UPDATED => 'Updated',
            self::DELETED => 'Deleted',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::CREATED => 'success',
            self::UPDATED => 'info',
            self::DELETED => 'danger',
        };
    }
}
