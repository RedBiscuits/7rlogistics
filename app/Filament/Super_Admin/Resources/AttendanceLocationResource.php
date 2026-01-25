<?php

namespace App\Filament\Super_Admin\Resources;

use App\Filament\Super_Admin\Resources\AttendanceLocationResource\Pages;
use App\Models\AttendanceLocation;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLocationResource extends Resource
{
    protected static ?string $model = AttendanceLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'System management';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Location Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Location Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Main Warehouse, Branch Office'),

                        Map::make('location')
                            ->label('Location on Map')
                            ->default([
                                'lat' => 30.0444,
                                'lng' => 31.2357,
                            ])
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                if ($state) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                }
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set): void {
                                if ($record) {
                                    $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                                }
                            })
                            ->extraStyles([
                                'min-height: 300px',
                                'max-height: 500px',
                                'width: 100%',
                            ])
                            ->liveLocation()
                            ->showMarker()
                            ->markerColor('primary')
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->draggable()
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->zoom(15)
                            ->detectRetina()
                            ->showMyLocationButton()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Update map when latitude is manually changed
                                if ($state && $get('longitude')) {
                                    $set('location', [
                                        'lat' => (float) $state,
                                        'lng' => (float) $get('longitude'),
                                    ]);
                                }
                            })
                            ->dehydrated(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Update map when longitude is manually changed
                                if ($state && $get('latitude')) {
                                    $set('location', [
                                        'lat' => (float) $get('latitude'),
                                        'lng' => (float) $state,
                                    ]);
                                }
                            })
                            ->dehydrated(),

                        Forms\Components\TextInput::make('radius')
                            ->label('Allowed Radius (meters)')
                            ->numeric()
                            ->required()
                            ->default(500)
                            ->minValue(100)
                            ->maxValue(5000)
                            ->step(50)
                            ->helperText('Drivers must be within this radius to check in'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active locations are available for check-in'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Statistics')
                    ->visible(fn (?AttendanceLocation $record) => $record !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('total_check_ins')
                            ->label('Total Check-ins')
                            ->content(fn (?AttendanceLocation $record) => $record ? $record->attendances()->count() : 0),

                        Forms\Components\Placeholder::make('last_check_in')
                            ->label('Last Check-in')
                            ->content(function (?AttendanceLocation $record) {
                                if (!$record) {
                                    return 'N/A';
                                }
                                $lastAttendance = $record->attendances()->latest('check_in_at')->first();
                                if (!$lastAttendance) {
                                    return 'No check-ins yet';
                                }
                                return $lastAttendance->check_in_at
                                    ->setTimezone(config('app.timezone'))
                                    ->format('Y-m-d H:i:s');
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('coordinates')
                    ->label('Coordinates')
                    ->getStateUsing(fn (AttendanceLocation $record) => "{$record->latitude}, {$record->longitude}")
                    ->copyable()
                    ->copyMessage('Coordinates copied!'),

                Tables\Columns\TextColumn::make('radius')
                    ->label('Radius')
                    ->suffix(' m')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('Total Check-ins')
                    ->counts('attendances')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All locations')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_on_map')
                    ->label('View on Map')
                    ->icon('heroicon-o-map')
                    ->url(fn (AttendanceLocation $record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}", shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Super_Admin\Resources\AttendanceLocationResource\RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLocations::route('/'),
            'create' => Pages\CreateAttendanceLocation::route('/create'),
            'edit' => Pages\EditAttendanceLocation::route('/{record}/edit'),
        ];
    }
}

