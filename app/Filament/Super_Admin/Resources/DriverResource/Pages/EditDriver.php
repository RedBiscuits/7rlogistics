<?php

namespace App\Filament\Super_Admin\Resources\DriverResource\Pages;

use App\Filament\Super_Admin\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only update password if it's provided (not empty)
        // The dehydrated() method on the form field already handles this,
        // but this provides an extra safety check
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
