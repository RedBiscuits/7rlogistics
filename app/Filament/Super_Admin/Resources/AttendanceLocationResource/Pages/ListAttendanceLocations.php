<?php

namespace App\Filament\Super_Admin\Resources\AttendanceLocationResource\Pages;

use App\Filament\Super_Admin\Resources\AttendanceLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceLocations extends ListRecords
{
    protected static string $resource = AttendanceLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


