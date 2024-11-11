<?php

namespace App\Filament\Finance\Resources\ClaimsResource;

use App\Filament\Finance\Resources\ClaimsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClaims extends EditRecord
{
    protected static string $resource = ClaimsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
