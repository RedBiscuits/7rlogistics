<?php

namespace App\Filament\Super_Admin\Resources\ContactRequestsResource\Pages;

use App\Filament\Super_Admin\Resources\ContactRequestsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactRequests extends CreateRecord
{
    protected static string $resource = ContactRequestsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
