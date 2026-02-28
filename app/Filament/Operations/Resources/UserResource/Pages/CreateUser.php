<?php

namespace App\Filament\Operations\Resources\UserResource\Pages;

use App\Filament\Operations\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $operationsRole = Role::where('name', 'Operations')->first();
        $data['roles'] = $operationsRole ? [$operationsRole->id] : [];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
