<?php

namespace App\Filament\Client\Resources\ShipmentResource\Pages;

use App\Enums\ShipmentStatus;
use App\Filament\Client\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewShipment extends ViewRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Download TCN')
                ->label('Download TCN')
                ->color('success')
                ->action(function (Shipment $record) {
                    $service = new ShipmentService($record);

                    return response()->download($service->downloadTcn());
                })
                ->disabled(function (Shipment $record) {
                    return in_array($record->status, [ShipmentStatus::CANCELLED, ShipmentStatus::PENDING]);
                })
                ->icon('heroicon-o-arrow-down-on-square'),
        ];
    }
}
