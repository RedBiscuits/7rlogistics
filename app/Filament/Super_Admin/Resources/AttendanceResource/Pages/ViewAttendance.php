<?php

namespace App\Filament\Super_Admin\Resources\AttendanceResource\Pages;

use App\Filament\Super_Admin\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
