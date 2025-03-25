<?php

namespace App\Filament\Operations\Resources\ContactRequestsResource\Pages;

use App\Filament\Operations\Resources\ContactRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactRequests extends ListRecords
{
    protected static string $resource = ContactRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
