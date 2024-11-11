<?php

namespace App\Filament\Super_Admin\Resources\TruckResource\Pages;

use App\Filament\Super_Admin\Resources\TruckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTruck extends CreateRecord
{
    protected static string $resource = TruckResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
