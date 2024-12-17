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
        $data['company_id'] = auth()->user()->company_id;
        
        $destination = auth()->user()->company->destinations()
            ->whereNotNull('price')
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('from_area', $data['from_area'])
                        ->where('to_area', $data['to_area']);
                })->orWhere(function ($q) use ($data) {
                    $q->where('to_area', $data['from_area'])
                        ->where('from_area', $data['to_area']);
                });
            })
            ->first();

        if ($destination) {

            $data['price'] = $destination->price;
        } else {
            redirect()->back()->with('error', 'Destination not found');
        }

        return $data;
    }


}
