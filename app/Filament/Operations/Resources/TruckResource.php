<?php

namespace App\Filament\Operations\Resources;

use App\Filament\Operations\Resources\TruckResource\Pages;
use App\Models\Truck;
use App\Models\TruckType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TruckResource extends Resource
{
    protected static ?string $model = Truck::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Transport';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('truck_type_id')
                    ->label('Truck Type')
                    ->required()
                    ->options(TruckType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')->nullable()->maxLength(255),
                Forms\Components\TextInput::make('plate_number')->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\TextInput::make('weight')->numeric()->minValue(0)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')->label('Code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('truckType.name')->label('Truck Type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Truck Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('weight')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrucks::route('/'),
            'create' => Pages\CreateTruck::route('/create'),
            // 'view' => Pages\ViewTruck::route('/{record}'),
            'edit' => Pages\EditTruck::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
