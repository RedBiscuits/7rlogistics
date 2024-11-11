<?php

namespace App\Filament\Client\Resources\DestinationResource\Pages;

use App\Filament\Client\Resources\DestinationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDestination extends CreateRecord
{
    protected static string $resource = DestinationResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $destination = auth()->user()->company->destinations()
            ->where('from_city', $data['from_city'])
            ->where('from_area', $data['from_area'])
            ->where('to_city', $data['to_city'])
            ->where('to_area', $data['to_area'])
            ->where('truck_id', $data['truck_id'])
            ->first();

        if ($destination) {
            return $destination;
        }

        $data['company_id'] = auth()->user()->company_id;

        return static::getModel()::create($data);
    }
}
