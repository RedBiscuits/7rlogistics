<?php

namespace App\Filament\Super_Admin\Resources\ShipmentResource\Pages;

use App\Actions\Shipment\CreateClaimAction;
use App\Filament\Super_Admin\Resources\ShipmentResource;
use App\Models\Invoice;
use App\Models\Mission;
use App\Models\Shipment;
use App\Models\TruckDestination;
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

        if ($data['status'] == 5) {
            Invoice::firstOrCreate([
                'shipment_id' => $record->id,
            ], [
                'price' => $record->price,
                'status' => 0,
                'payment_type' => 0,
                'collected_at' => null,
                'reference' => null,
                'notes' => null,
            ]);

            Mission::firstOrCreate([
                'shipment_id' => $record->id,
            ], [
                'price' => TruckDestination::where('truck_type_id', $record->truck->truck_type_id)
                    ->where(function ($query) use ($record) {
                        $query->where(function ($q) use ($record) {
                            $q->where('from_area', $record->from_area)
                                ->where('to_area', $record->to_area);
                        })->orWhere(function ($q) use ($record) {
                            $q->where('from_area', $record->to_area)
                                ->where('to_area', $record->from_area);
                        });
                    })->first()->price ?? 0,
                'status' => 'pending',
                'from_area' => $record->from_area,
                'to_area' => $record->to_area,
                'date' => $record->date,
                'time' => $record->time,
                'driver_id' => $record->driver_id ?? $data['driver_id'],
                'truck_id' => $record->truck_id ?? $data['truck_id'],
                'note' => 'Mission created automatically',
            ]);

            // Handle multiple claims
            if (isset($data['has_claim']) && $data['has_claim'] && isset($data['claim_packages'])) {
                foreach ($data['claim_packages'] as $claim) {
                    CreateClaimAction::execute(
                        $record,
                        $claim['claim_type'],
                        $claim['amount'],
                        $claim['quantity']
                    );
                }
            }
        }
        unset($data['has_claim']);
        unset($data['claim_packages']);

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
