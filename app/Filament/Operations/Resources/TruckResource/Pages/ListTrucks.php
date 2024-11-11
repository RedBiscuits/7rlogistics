<?php

namespace App\Filament\Operations\Resources\TruckResource\Pages;

use App\Filament\Operations\Resources\TruckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrucks extends ListRecords
{
    protected static string $resource = TruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
