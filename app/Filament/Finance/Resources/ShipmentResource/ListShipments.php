<?php

namespace App\Filament\Finance\Resources\ShipmentResource;

use App\Filament\Finance\Resources\FinanceShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipments extends ListRecords
{
    protected static string $resource = FinanceShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
        ];
    }
}
