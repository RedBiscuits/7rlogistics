<?php

namespace App\Filament\Super_Admin\Resources\MissionResource\Pages;

use App\Filament\Super_Admin\Resources\MissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMissions extends ListRecords
{
    protected static string $resource = MissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
