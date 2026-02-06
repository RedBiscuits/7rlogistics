<?php

namespace App\Filament\Super_Admin\Resources\AttendanceResource\Pages;

use App\Filament\Super_Admin\Resources\AttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

