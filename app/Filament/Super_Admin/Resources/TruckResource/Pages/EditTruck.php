<?php

namespace App\Filament\Super_Admin\Resources\TruckResource\Pages;

use App\Filament\Super_Admin\Resources\TruckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTruck extends EditRecord
{
    protected static string $resource = TruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
