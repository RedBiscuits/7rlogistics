<?php

namespace App\Filament\Operations\Resources\TruckResource\Pages;

use App\Filament\Operations\Resources\TruckResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTruck extends ViewRecord
{
    protected static string $resource = TruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
