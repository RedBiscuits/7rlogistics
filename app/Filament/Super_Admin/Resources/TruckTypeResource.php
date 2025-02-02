<?php

namespace App\Filament\Super_Admin\Resources;

use App\Filament\Super_Admin\Resources\TruckTypeResource\Pages;
use App\Models\TruckType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Super_Admin\Resources\TruckTypeResource\RelationManagers\TrucksDestinationsRelationManager;
class TruckTypeResource extends Resource
{
    protected static ?string $model = TruckType::class;

    protected static ?string $navigationIcon = 'heroicon-o-chevron-double-up';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Transport';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255)->label('Truck Type Name')->live(),
                Forms\Components\TextInput::make('capacity')->numeric()->minValue(0)->nullable()->label('Capacity (kg)')->live(),
                Forms\Components\TextInput::make('weight')->numeric()->minValue(0)->nullable()->label('Weight (kg)')->live(),
                Forms\Components\TextInput::make('length')->numeric()->minValue(0)->nullable()->label('Length (cm)'),
                Forms\Components\TextInput::make('width')->numeric()->minValue(0)->nullable()->label('Width (cm)'),
                Forms\Components\TextInput::make('height')->numeric()->minValue(0)->nullable()->label('Height (cm)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('capacity')->label('Capacity (kg)')->sortable()->searchable()
                    ->getStateUsing(fn($record) => $record->capacity ?? "--"),
                Tables\Columns\TextColumn::make('weight')->label('Weight (kg)')->sortable()->searchable()
                    ->getStateUsing(fn($record) => $record->weight ?? "--"),
                Tables\Columns\TextColumn::make('length')->label('Length (m)')->sortable()->searchable()
                    ->getStateUsing(fn($record) => $record->length ?? "--"),
                Tables\Columns\TextColumn::make('width')->label('Width (m)')->sortable()->searchable()
                    ->getStateUsing(fn($record) => $record->width ?? "--"),
                Tables\Columns\TextColumn::make('height')->label('Height (m)')->sortable()->searchable()
                    ->getStateUsing(fn($record) => $record->height ?? "--"),

                Tables\Columns\TextColumn::make('trucks_count')->counts('trucks')
                    ->label('Trucks Count')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('trucks_destinations_count')->counts('trucksDestinations')
                    ->label('Trucks Destinations Count')->sortable()->searchable(),
            ])
            ->filters([])
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
            TrucksDestinationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTruckTypes::route('/'),
            'create' => Pages\CreateTruckType::route('/create'),
            'edit' => Pages\EditTruckType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
