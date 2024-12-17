<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\DestinationResource\Pages;
use App\Models\Area;
use App\Models\City;
use App\Models\Destination;
use App\Models\Truck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Location';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('from_city')
                    ->label('From City')
                    ->options(function (callable $get) {
                        $cites = City::get()->pluck('name', 'id');

                        return $cites ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(fn (callable $set) => $set('from_area', null))
                    ->required(),

                Forms\Components\Select::make('from_area')
                    ->label('From Area')
                    ->options(function (callable $get) {
                        $cityId = $get('from_city');
                        if ($cityId) {
                            return Area::where('city_id', $cityId)->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('to_city')
                    ->label('To City')
                    ->options(function (callable $get) {
                        $cites = City::get()->pluck('name', 'id');

                        return $cites ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(fn (callable $set) => $set('to_area', null))
                    ->required(),

                Forms\Components\Select::make('to_area')
                    ->label('To Area')
                    ->options(function (callable $get) {
                        $cityId = $get('to_city');
                        if ($cityId) {
                            return Area::where('city_id', $cityId)->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('truck_id')
                    ->label('Truck')
                    ->options(function (callable $get) {
                        $trucks = Truck::all()->pluck('name', 'id');

                        return $trucks ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('company_id', auth()->user()->company_id);
            })
            ->columns([
                Tables\Columns\TextColumn::make('fromArea.city.name')->label('From City')->searchable(),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area')->searchable(),
                Tables\Columns\TextColumn::make('toArea.city.name')->label('To City')->searchable(),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area')->searchable(),
                Tables\Columns\TextColumn::make('truck.name')->label('Truck'),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP')->hidden(fn ($livewire) => $livewire->activeTab === 'Pending'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDestination::route('/'),
            'create' => Pages\CreateDestination::route('/create'),
            // 'view' => Pages\ViewDestination::route('/{record}'),
            // 'edit' => Pages\EditDestination::route('/{record}/edit'),
        ];
    }
}
