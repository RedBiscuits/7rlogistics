<?php

namespace App\Filament\Super_Admin\Resources\ContactRequestsResource\Pages;

use App\Filament\Super_Admin\Resources\ContactRequestsResource;
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
