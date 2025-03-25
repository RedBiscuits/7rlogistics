<?php

namespace App\Filament\Super_Admin\Resources\ContactRequestsResource\Pages;

use App\Filament\Super_Admin\Resources\ContactRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactRequests extends EditRecord
{
    protected static string $resource = ContactRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
