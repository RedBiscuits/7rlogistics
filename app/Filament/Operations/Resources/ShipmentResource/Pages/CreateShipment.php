<?php

namespace App\Filament\Operations\Resources\ShipmentResource\Pages;

use App\Filament\Operations\Resources\ShipmentResource;
use App\Models\Destination;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Company;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Company::find($data['company_id'])->users()->first();
        $data['user_id'] = $user->id;

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

        if ($destination) {
            $data['price'] = $destination->price;
        } else {
            redirect()->back()->with('error', 'Destination not found');
        }

        if ($data['status'] == 0 || $data['status'] == 1) {
            $data['driver_id'] = null;
        }

        unset($data['company_id']);

        return $data;
    }
}
