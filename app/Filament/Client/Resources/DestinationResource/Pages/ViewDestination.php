<?php

namespace App\Filament\Client\Resources\DestinationResource\Pages;

use App\Filament\Client\Resources\DestinationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDestination extends ViewRecord
{
    protected static string $resource = DestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
