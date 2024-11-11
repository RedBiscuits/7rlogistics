<?php

namespace App\Filament\Finance\Resources\ClaimsResource;

use App\Filament\Finance\Resources\ClaimsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClaims extends ListRecords
{
    protected static string $resource = ClaimsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
