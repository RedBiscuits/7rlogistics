<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\CompanyResource\Pages;
use App\Filament\Operations\Resources\CompanyResource\RelationManagers\CitiesRelationManager;
use App\Filament\Operations\Resources\CompanyResource\RelationManagers\DestinationsRelationManager;
use App\Filament\Operations\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use App\Filament\Operations\Resources\CompanyResource\RelationManagers\ShipmentRelationManager;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'System management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('industry')->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('address')->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('phone')->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->minLength(3)->maxLength(255),
                Forms\Components\TextInput::make('website')->minLength(3)->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('industry')->sortable()->searchable()->getStateUsing(function ($record) {
                    return $record->industry ?? 'N/A';
                }),
                Tables\Columns\TextColumn::make('address')->sortable()->searchable()->getStateUsing(function ($record) {
                    return $record->address ?? 'N/A';
                }),
                Tables\Columns\TextColumn::make('phone')->sortable()->searchable()->getStateUsing(function ($record) {
                    return $record->phone ?? 'N/A';
                }),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable()->getStateUsing(function ($record) {
                    return $record->email ?? 'N/A';
                }),
                Tables\Columns\TextColumn::make('website')->sortable()->searchable()->getStateUsing(function ($record) {
                    return $record->website ?? 'N/A';
                }),
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
            CitiesRelationManager::class,
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
}
