<?php

namespace App\Filament\Super_Admin\Resources\ShipmentResource\Pages;

use App\Filament\Super_Admin\Resources\ShipmentResource;
use App\Models\Destination;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Model;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {


        if ($data['status'] == 0 || $data['status'] == 1) {
            $data['driver_id'] = null;
        }

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {

        return [
            Actions\DeleteAction::make(),
            Action::make('Download_TCN')
                ->label('Download TCN')
                ->color(Color::Emerald)
                ->action(function (Shipment $record) {
                    $service = new ShipmentService($record);

                    return response()->download($service->downloadTcn());
                })
                ->icon('heroicon-o-arrow-down-on-square'),
            Action::make('Download_DN')
                ->label('Download DN')
                ->color(Color::Zinc)
                ->action(function (Shipment $record) {
                    $service = new ShipmentService($record);

                    return response()->download($service->downloadDn());
                })
                ->icon('heroicon-o-arrow-down-on-square'),
            Action::make('Download_MIS')
                ->label('Download MIS')
                ->color(Color::Purple)
                ->action(function (Shipment $record) {
                    $service = new ShipmentService($record);

                    return response()->download($service->downloadMis());
                })
                ->icon('heroicon-o-arrow-down-on-square'),
            Action::make('Download_Invoice')
                ->label('Download Invoice')
                ->color(Color::Indigo)
                ->action(function (Shipment $record) {
                    $service = new ShipmentService($record);

                    return response()->download($service->downloadInvoice());
                })
                ->icon('heroicon-o-arrow-down-on-square'),
        ];
    }
}
