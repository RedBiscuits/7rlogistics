<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\AttendanceResource\Pages;
use App\Models\DriverAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = DriverAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'System management';

    protected static ?string $navigationLabel = 'Attendances';

    protected static ?string $modelLabel = 'Attendance';

    protected static ?string $pluralModelLabel = 'Attendances';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance details')
                    ->schema([
                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->relationship('driver', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('attendance_location_id')
                            ->label('Location')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required(),
                        Forms\Components\DateTimePicker::make('check_in_at')
                            ->label('Check-in'),
                        Forms\Components\DateTimePicker::make('check_out_at')
                            ->label('Check-out'),
                        Forms\Components\TextInput::make('hours_worked')
                            ->label('Hours worked')
                            ->numeric()
                            ->suffix(' hrs'),
                        Forms\Components\Placeholder::make('check_in_coords')
                            ->label('Check-in coordinates')
                            ->content(fn (?DriverAttendance $record) => $record && $record->check_in_latitude && $record->check_in_longitude
                                ? "{$record->check_in_latitude}, {$record->check_in_longitude}"
                                : 'N/A'),
                        Forms\Components\Placeholder::make('check_out_coords')
                            ->label('Check-out coordinates')
                            ->content(fn (?DriverAttendance $record) => $record && $record->check_out_latitude && $record->check_out_longitude
                                ? "{$record->check_out_latitude}, {$record->check_out_longitude}"
                                : 'N/A'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Check-in')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out_at')
                    ->label('Check-out')
                    ->dateTime('d-m-Y H:i')
                    ->placeholder('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' hrs')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->check_out_at ? 'completed' : 'checked_in')
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
                // Date range filter
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from_date'] ?? null,
                                fn ($query, $date) => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'] ?? null,
                                fn ($query, $date) => $query->whereDate('date', '<=', $date),
                            );
                    }),

                // Driver filter
                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('Driver')
                    ->relationship('driver', 'name')
                    ->searchable(),

                // Location filter
                Tables\Filters\SelectFilter::make('attendance_location_id')
                    ->label('Location')
                    ->relationship('location', 'name')
                    ->searchable(),

                // Status filter
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'checked_in' => 'Checked In',
                        'completed' => 'Completed',
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                ($data['value'] ?? null) === 'checked_in',
                                fn ($query) => $query->whereNull('check_out_at'),
                            )
                            ->when(
                                ($data['value'] ?? null) === 'completed',
                                fn ($query) => $query->whereNotNull('check_out_at'),
                            );
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

