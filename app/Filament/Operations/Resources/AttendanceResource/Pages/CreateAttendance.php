<?php

namespace App\Filament\Operations\Resources\AttendanceResource\Pages;

use App\Filament\Operations\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
