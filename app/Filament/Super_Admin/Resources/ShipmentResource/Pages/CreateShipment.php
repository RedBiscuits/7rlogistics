<?php

namespace App\Filament\Super_Admin\Resources\ShipmentResource\Pages;

use App\Filament\Super_Admin\Resources\ShipmentResource;
use App\Models\Destination;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Company;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
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
            ->where('truck_id' , $data['truck_id'])
            ->first();

        // if ($destination) {
        //     $data['price'] = $destination->price;
        // } else {
        //     redirect()->back()->with('error', 'Destination not found');
        // }

        if ($data['status'] == 0 || $data['status'] == 1) {
            $data['driver_id'] = null;
        }


        return $data;
    }
}
