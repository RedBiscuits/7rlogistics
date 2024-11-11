<?php

namespace App\Filament\Operations\Resources\TruckResource\Pages;

use App\Filament\Operations\Resources\TruckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTruck extends CreateRecord
{
    protected static string $resource = TruckResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
