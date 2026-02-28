<?php

namespace App\Filament\Operations\Widgets;

use App\Models\DriverAttendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AttendanceReports extends BaseWidget
{
    protected static ?string $heading = 'Attendance Reports';

    protected static ?int $sort = 11;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DriverAttendance::query()
                    ->with(['driver', 'location'])
                    ->orderBy('date', 'desc')
                    ->orderBy('check_in_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Driver Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Check-in Time')
                    ->dateTime('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out_at')
                    ->label('Check-out Time')
                    ->dateTime('H:i')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours Worked')
                    ->numeric(
                        decimalPlaces: 2,
                    )
                    ->suffix(' hrs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn (DriverAttendance $record) => $record->check_out_at ? 'completed' : 'checked_in')
                    ->icon(fn (string $state): string => match ($state) {
                        'completed' => 'heroicon-o-check-circle',
                        'checked_in' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'checked_in' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('Driver')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->defaultPaginationPageOption(25);
    }

    protected function getTableSummary(): array
    {
        return [
            Tables\Columns\Summarizers\Sum::make('hours_worked')
                ->label('Total Hours')
                ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2) . ' hrs'),
            Tables\Columns\Summarizers\Count::make()
                ->label('Total Check-ins'),
            Tables\Columns\Summarizers\Average::make('hours_worked')
                ->label('Average Hours')
                ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2) . ' hrs'),
        ];
    }
}

