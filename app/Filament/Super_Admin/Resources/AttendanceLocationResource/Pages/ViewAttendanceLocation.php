<?php

namespace App\Filament\Super_Admin\Resources\AttendanceLocationResource\Pages;

use App\Filament\Super_Admin\Resources\AttendanceLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceLocation extends ViewRecord
{
    protected static string $resource = AttendanceLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
