<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ModelChangeSource: string implements HasLabel, HasColor
{
    case API = 'api';
    case OPERATIONS_PANEL = 'operations_panel';
    case CONSOLE = 'console';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::API => 'API',
            self::OPERATIONS_PANEL => 'Operations panel',
            self::CONSOLE => 'Console',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::API => 'info',
            self::OPERATIONS_PANEL => 'warning',
            self::CONSOLE => 'gray',
        };
    }
}
