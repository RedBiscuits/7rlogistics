<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\DriverResource\Pages;
use App\Filament\Operations\Resources\DriverResource\RelationManagers\AttendancesRelationManager;
use App\Filament\Operations\Resources\DriverResource\RelationManagers\MissionsRelationManager;
use App\Models\Driver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'System management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),

                TextInput::make('code')
                    ->required()
                    ->maxLength(255),

                TextInput::make('id_number')
                    ->maxLength(255),

                DatePicker::make('id_expire_date'),

                TextInput::make('driving_license_number')
                    ->maxLength(255),

                DatePicker::make('driving_license_expire_date'),

                \Filament\Forms\Components\Select::make('license_type')
                    ->label('License Type')
                    ->options([
                        'A' => 'Type A (Motorcycle)',
                        'B' => 'Type B (Car)',
                        'C' => 'Type C (Truck)',
                        'D' => 'Type D (Bus)',
                        'E' => 'Type E (Trailer)',
                    ])
                    ->searchable()
                    ->nullable(),

                TextInput::make('car_type')
                    ->maxLength(255),

                TextInput::make('car_license')
                    ->maxLength(255),

                DatePicker::make('car_license_expire_date'),

                TextInput::make('location')
                    ->maxLength(255),

                TextInput::make('city')
                    ->maxLength(255),

                TextInput::make('contact_number')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('contact_number_2')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('contact_number_3')
                    ->tel()
                    ->maxLength(255),

                \Filament\Forms\Components\Placeholder::make('biometric_enabled')
                    ->label('Biometric Authentication')
                    ->content(fn (?Driver $record) => $record && $record->hasBiometricEnabled() ? 'Enabled' : 'Disabled')
                    ->badge()
                    ->color(fn (?Driver $record) => $record && $record->hasBiometricEnabled() ? 'success' : 'gray'),

                SpatieMediaLibraryFileUpload::make('license_front')
                    ->collection('license_front')
                    ->label('License Front Photo')
                    ->downloadable()
                    ->openable()
                    ->image()
                    ->previewable(),

                SpatieMediaLibraryFileUpload::make('license_back')
                    ->collection('license_back')
                    ->label('License Back Photo')
                    ->downloadable()
                    ->openable()
                    ->image()
                    ->previewable(),

                SpatieMediaLibraryFileUpload::make('id_photo')
                    ->collection('id_photo')
                    ->label('ID Photo')
                    ->downloadable()
                    ->openable()
                    ->image()
                    ->previewable(),

                SpatieMediaLibraryFileUpload::make('profile_photo')
                    ->collection('profile_photo')
                    ->label('Profile Photo')
                    ->downloadable()
                    ->openable()
                    ->image()
                    ->previewable(),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('attachments')
                    ->label('Attachments')
                    ->downloadable()
                    ->openable()
                    ->multiple()
                    ->maxFiles(5)
                    ->previewable(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->default('N/A'),
                TextColumn::make('code')
                    ->searchable()
                    ->default('N/A'),
                TextColumn::make('city')
                    ->searchable()
                    ->default('N/A'),
                TextColumn::make('contact_number')
                    ->searchable()
                    ->default('N/A'),
                TextColumn::make('id_expire_date')
                    ->date()
                    ->sortable()
                    ->default(null),
                TextColumn::make('driving_license_expire_date')
                    ->date()
                    ->sortable()
                    ->default(null)
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->driving_license_expire_date) {
                            return 'gray';
                        }
                        $days = $record->getLicenseExpiryDays();
                        if ($days <= 10) {
                            return 'danger';
                        }
                        if ($days <= 30) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->formatStateUsing(function ($record) {
                        if (!$record->driving_license_expire_date) {
                            return 'Not set';
                        }
                        $days = $record->getLicenseExpiryDays();
                        return $record->driving_license_expire_date->format('Y-m-d') . " ({$days} days)";
                    }),
                TextColumn::make('license_type')
                    ->label('License Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('N/A'),
                TextColumn::make('car_license_expire_date')
                    ->date()
                    ->sortable()
                    ->default(null),
                SpatieMediaLibraryImageColumn::make('attachments')
                    ->label('Attachments')
                    ->collection('attachments'),
            ])
            ->filters([
                Tables\Filters\Filter::make('license_expiring_soon')
                    ->label('License Expiring Soon (< 30 days)')
                    ->query(fn ($query) => $query->whereNotNull('driving_license_expire_date')
                        ->where('driving_license_expire_date', '<=', now()->addDays(30))
                        ->where('driving_license_expire_date', '>', now())),
                Tables\Filters\Filter::make('license_blocked')
                    ->label('License Blocked (< 10 days)')
                    ->query(fn ($query) => $query->whereNotNull('driving_license_expire_date')
                        ->where('driving_license_expire_date', '<=', now()->addDays(10))
                        ->where('driving_license_expire_date', '>', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            MissionsRelationManager::class,
            AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
