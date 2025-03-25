<?php

namespace App\Filament\Operations\Resources\ContactRequestsResource\Pages;

use App\Filament\Operations\Resources\ContactRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactRequests extends CreateRecord
{
    protected static string $resource = ContactRequestsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
