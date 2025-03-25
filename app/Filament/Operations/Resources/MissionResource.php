<?php

namespace App\Filament\Operations\Resources;

use App\Enums\ShipmentStatus;
use App\Filament\Operations\Resources\MissionResource\Pages;
use App\Models\Mission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Freight management';

    public static ?string $label = 'Mission';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('shipment_id')
                    ->relationship(
                        'shipment',
                        'id',
                        fn (Builder $query) => $query->where('status', '=', ShipmentStatus::DELIVERED)
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - "
                        .($record->company->name ?? 'No Company').' - '
                        .($record->truck->plate_number ?? 'No Truck'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (! $state) {
                            return;
                        }

                        $shipment = \App\Models\Shipment::find($state);
                        if ($shipment) {
                            $set('driver_id', $shipment->driver_id);
                            $set('truck_id', $shipment->truck_id);
                            $set('from_area', $shipment->from_area);
                            $set('to_area', $shipment->to_area);
                        }
                    })
                    ->required(),

                Forms\Components\Select::make('driver_id')
                    ->relationship('driver', 'name')
                    ->required(),

                Forms\Components\Select::make('truck_id')
                    ->relationship('truck', 'plate_number')
                    ->required(),

                Forms\Components\Select::make('from_area')
                    ->relationship('fromArea', 'name')
                    ->required(),

                Forms\Components\Select::make('to_area')
                    ->relationship('toArea', 'name')
                    ->required(),

                Forms\Components\DatePicker::make('date'),
                Forms\Components\TimePicker::make('time'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('note'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shipment.id')->label('Shipment Code'),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('driver.name')->label('Driver'),
                Tables\Columns\TextColumn::make('driver.code')->label('Driver Code'),
                Tables\Columns\TextColumn::make('truck.plate_number')->label('Truck'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('fromArea')
                    ->relationship('fromArea', 'name'),
                Tables\Filters\SelectFilter::make('toArea')
                    ->relationship('toArea', 'name'),
                Tables\Filters\SelectFilter::make('driver')
                    ->relationship('driver', 'name'),
                Tables\Filters\SelectFilter::make('truck')
                    ->relationship('truck', 'plate_number'),
                Tables\Filters\SelectFilter::make('shipment')
                    ->relationship('shipment', 'id'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Mission $record) {
                        $record->update(['status' => 'paid']);
                    }),
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
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
