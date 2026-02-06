<?php

namespace App\Filament\Super_Admin\Resources\AttendanceResource\Pages;

use App\Filament\Super_Admin\Resources\AttendanceResource;
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
