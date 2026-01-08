<?php

namespace App\Filament\Super_Admin\Resources;

use App\Filament\Super_Admin\Resources\DriverResource\Pages;
use App\Filament\Super_Admin\Resources\DriverResource\RelationManagers\MissionsRelationManager;
use App\Models\Driver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
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

                Placeholder::make('password_status')
                    ->label('Current Password Status')
                    ->content(function (?Driver $record) {
                        if (!$record) {
                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No password set</span>');
                        }
                        if ($record->hasPassword()) {
                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Password is set</span>');
                        }
                        return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No password set</span>');
                    })
                    ->visible(fn (?Driver $record) => $record !== null),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Password')
                    ->helperText(function (?Driver $record) {
                        if ($record && $record->hasPassword()) {
                            return 'Enter a new password to change it, or leave empty to keep the current password.';
                        }
                        return 'Set a password for driver login. Leave empty to use biometric authentication only.';
                    })
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),

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
                    ->default(null),
                TextColumn::make('car_license_expire_date')
                    ->date()
                    ->sortable()
                    ->default(null),
                SpatieMediaLibraryImageColumn::make('attachments')
                    ->label('Attachments')
                    ->collection('attachments'),
            ])
            ->filters([
                //
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
