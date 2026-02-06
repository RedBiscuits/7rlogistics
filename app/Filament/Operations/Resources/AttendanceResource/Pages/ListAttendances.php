<?php

namespace App\Filament\Operations\Resources\AttendanceResource\Pages;

use App\Filament\Operations\Resources\AttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        // No create action – attendances come from the driver app/API.
        return [];
    }
}

