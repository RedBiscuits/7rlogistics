<?php

namespace App\Filament\Operations\Resources\ShipmentResource\Pages;

use App\Enums\ShipmentStatus;
use App\Filament\Operations\Resources\ShipmentResource;
use App\Models\Destination;
use App\Models\Truck;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Operations can only create shipments as Pending; price comes from destination.
        $data['status'] = ShipmentStatus::PENDING->value;

        $destination = Destination::where('company_id', $data['company_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('from_area', $data['from_area'])
                        ->where('to_area', $data['to_area']);
                })->orWhere(function ($q) use ($data) {
                    $q->where('to_area', $data['from_area'])
                        ->where('from_area', $data['to_area']);
                });
            })
            ->where('truck_type_id', Truck::find($data['truck_id'])->truckType->id)
            ->whereNotNull('price')
            ->first();

        if ($destination) {
            $data['price'] = $destination->price;
        } else {
            redirect()->back()->with('error', 'No destination found');
        }

        // Allow driver assignment when Pending; do not clear driver_id.

        return $data;
    }
}
