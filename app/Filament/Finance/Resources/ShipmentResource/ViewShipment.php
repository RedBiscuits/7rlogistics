<?php

namespace App\Filament\Finance\Resources\ShipmentResource;

use App\Filament\Finance\Resources\FinanceShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShipment extends ViewRecord
{
    protected static string $resource = FinanceShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
