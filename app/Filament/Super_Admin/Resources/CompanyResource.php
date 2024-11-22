<?php

namespace App\Filament\Super_Admin\Resources;

use App\Filament\Super_Admin\Resources\CompanyResource\Pages;
use App\Filament\Super_Admin\Resources\CompanyResource\RelationManagers\CitiesRelationManager;
use App\Filament\Super_Admin\Resources\CompanyResource\RelationManagers\DestinationsRelationManager;
use App\Filament\Super_Admin\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use App\Filament\Super_Admin\Resources\CompanyResource\RelationManagers\ShipmentRelationManager;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'System management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tax_number_expires_at')
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_person')
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('attachments')
                    ->label('Attachments')
                    ->downloadable()
                    ->openable()
                    ->multiple()
                    ->maxFiles(5)
                    ->previewable(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number_expires_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('attachments')
                    ->label('Attachments')
                    ->collection('attachments'),
            ])
            ->filters([])
            ->actions([
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
            UsersRelationManager::class,
            // CitiesRelationManager::class,
            DestinationsRelationManager::class,
            ShipmentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
