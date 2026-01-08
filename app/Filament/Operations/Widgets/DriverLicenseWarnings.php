<?php

namespace App\Filament\Operations\Widgets;

use App\Models\Driver;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DriverLicenseWarnings extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Driver License Warnings';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Driver::query()
                    ->whereNotNull('driving_license_expire_date')
                    ->where('driving_license_expire_date', '<=', now()->addDays(30))
                    ->where('driving_license_expire_date', '>', now())
                    ->orderBy('driving_license_expire_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Driver Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->label('Driver Code')->searchable(),
                Tables\Columns\TextColumn::make('driving_license_number')->label('License Number'),
                Tables\Columns\TextColumn::make('driving_license_expire_date')
                    ->label('Expiry Date')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_expiry_days')
                    ->label('Days Remaining')
                    ->getStateUsing(function ($record) {
                        $days = $record->getLicenseExpiryDays();
                        return $days !== null ? $days : 'N/A';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $days = $record->getLicenseExpiryDays();
                        if ($days === null) {
                            return 'gray';
                        }
                        if ($days <= 10) {
                            return 'danger';
                        }
                        if ($days <= 30) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->sortable(),
            ]);
    }
}








