<?php

namespace App\Filament\Operations\Resources\TruckTypeResource\Pages;

use App\Filament\Operations\Resources\TruckTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTruckTypes extends ListRecords
{
    protected static string $resource = TruckTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
