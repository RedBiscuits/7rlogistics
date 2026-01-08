<?php

namespace App\Filament\Operations\Resources\DriverResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Attendance History';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Check-in')
                    ->dateTime('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out_at')
                    ->label('Check-out')
                    ->dateTime('H:i')
                    ->default('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' hrs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->default('N/A')
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

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'checked_in' => 'Checked In',
                        'completed' => 'Completed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'checked_in') {
                            return $query->whereNull('check_out_at');
                        }
                        if ($data['value'] === 'completed') {
                            return $query->whereNotNull('check_out_at');
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                // No create action - attendances are created via API
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only form for viewing attendance details
                Forms\Components\Placeholder::make('date')
                    ->label('Date')
                    ->content(fn ($record) => $record->date->format('d-m-Y')),

                Forms\Components\Placeholder::make('check_in_at')
                    ->label('Check-in Time')
                    ->content(fn ($record) => $record->check_in_at->format('Y-m-d H:i:s')),

                Forms\Components\Placeholder::make('check_out_at')
                    ->label('Check-out Time')
                    ->content(fn ($record) => $record->check_out_at ? $record->check_out_at->format('Y-m-d H:i:s') : 'Not checked out'),

                Forms\Components\Placeholder::make('hours_worked')
                    ->label('Hours Worked')
                    ->content(fn ($record) => $record->hours_worked ? number_format($record->hours_worked, 2) . ' hours' : 'N/A'),

                Forms\Components\Placeholder::make('location')
                    ->label('Location')
                    ->content(fn ($record) => $record->location ? $record->location->name : 'N/A'),
            ]);
    }
}







