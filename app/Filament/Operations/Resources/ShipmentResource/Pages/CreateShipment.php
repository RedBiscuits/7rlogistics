<?php

namespace App\Filament\Operations\Resources\ShipmentResource\Pages;

use App\Filament\Operations\Resources\ShipmentResource;
use App\Models\Destination;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $destination = Destination::where('user_id', $data['user_id'])
            ->whereNotNull('price')
            ->where('from_area', $data['from_area'])
            ->where('to_area', $data['to_area'])
            ->first();

        if ($destination) {
            $data['price'] = $destination->price;
        } else {
            redirect()->back()->with('error', 'Destination not found');
        }

        if ($data['status'] == 0 || $data['status'] == 1) {
            $data['driver_id'] = null;
        }

        return $data;
    }
}
