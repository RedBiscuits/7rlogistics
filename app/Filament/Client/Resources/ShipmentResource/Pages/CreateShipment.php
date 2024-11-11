<?php

namespace App\Filament\Client\Resources\ShipmentResource\Pages;

use App\Filament\Client\Resources\ShipmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;

        $destination = auth()->user()->company->destinations()
            ->whereNotNull('price')
            ->where('from_area', $data['from_area'])
            ->where('to_area', $data['to_area'])
            ->first();

        if ($destination) {

            $data['price'] = $destination->price;
        } else {
            redirect()->back()->with('error', 'Destination not found');
        }

        return $data;
    }


}
