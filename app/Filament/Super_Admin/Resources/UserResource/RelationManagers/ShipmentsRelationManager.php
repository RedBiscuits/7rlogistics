<?php

namespace App\Filament\Super_Admin\Resources\UserResource\RelationManagers;

use App\Filament\Super_Admin\Resources\ShipmentResource;
use App\Models\Area;
use App\Models\City;
use App\Models\Destination;
use App\Models\Truck;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make('Receiver Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('receiver_name')
                                    ->label('Receiver Name')
                                    ->required(),

                                Forms\Components\TextInput::make('receiver_phone')
                                    ->label('Receiver Phone')
                                    ->required()
                                    ->tel(),

                                Forms\Components\TextInput::make('receiver_address')
                                    ->label('Receiver Address')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Card::make('Destination Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('from_city')
                                    ->label('From City')
                                    ->options(City::where('user_id', $this->getOwnerRecord()->id)->pluck('name', 'id'))
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('City Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return City::create($data)->id;
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('from_area', null))
                                    ->required(),

                                Forms\Components\Select::make('from_area')
                                    ->label('From Area')
                                    ->options(function (callable $get, callable $set) {
                                        $cityId = $get('from_city');
                                        if ($cityId) {
                                            return $this->getOwnerRecord()->destinations()
                                                ->when($get('to_area'), fn ($q) => $q->where('to_area', $get('to_area')))
                                                ->whereNotNull('price')
                                                ->whereHas('fromArea', function ($q) use ($cityId) {
                                                    $q->where('city_id', $cityId);
                                                })
                                                ->with('fromArea')
                                                ->get()
                                                ->pluck('fromArea.name', 'fromArea.id')
                                                ->toArray();
                                        }

                                        return [];
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\Select::make('city_id')
                                            ->label('City')
                                            ->options(City::where('user_id', $this->getOwnerRecord()->id)->pluck('name', 'id'))
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Area Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return Area::create($data)->id;
                                    })
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $fromArea = $get('from_area');
                                        $toArea = $get('to_area');
                                        if ($fromArea && $toArea) {
                                            $destination = $this->getOwnerRecord()->destinations()->where('from_area', $fromArea)
                                                ->where('to_area', $toArea)
                                                ->whereNotNull('price')
                                                ->first();
                                            if ($destination) {
                                                $set('price', $destination->price);
                                            }
                                        }
                                    })
                                    ->required(),

                                Forms\Components\Select::make('to_city')
                                    ->label('To City')
                                    ->options(City::where('user_id', $this->getOwnerRecord()->id)->pluck('name', 'id'))
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('City Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return City::create($data)->id;
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('to_area', null))
                                    ->required(),

                                Forms\Components\Select::make('to_area')
                                    ->label('To Area')
                                    ->options(function (callable $get, callable $set) {
                                        $cityId = $get('to_city');
                                        if ($cityId) {
                                            return $this->getOwnerRecord()->destinations()
                                                ->when($get('from_area'), fn ($q) => $q->where('from_area', $get('from_area')))
                                                ->whereNotNull('price')
                                                ->whereHas('toArea', function ($q) use ($cityId) {
                                                    $q->where('city_id', $cityId);
                                                })
                                                ->with('toArea')
                                                ->get()
                                                ->pluck('toArea.name', 'toArea.id')
                                                ->toArray();
                                        }

                                        return [];
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\Select::make('city_id')
                                            ->label('City')
                                            ->options(City::where('user_id', $this->getOwnerRecord()->id)->pluck('name', 'id'))
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Area Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return Area::create($data)->id;
                                    })
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $fromArea = $get('from_area');
                                        $toArea = $get('to_area');
                                        if ($fromArea && $toArea) {
                                            $destination = $this->getOwnerRecord()->destinations()->where('from_area', $fromArea)
                                                ->where('to_area', $toArea)
                                                ->whereNotNull('price')
                                                ->first();
                                            if ($destination) {
                                                $set('price', $destination->price);
                                            }
                                        }
                                    })
                                    ->required(),

                                // Forms\Components\TextInput::make('price')
                                //     ->label('Price')
                                //     ->reactive()
                                //     ->required()
                                //     ->readOnly()
                                //     ->disabled()
                            ]),
                    ]),

                Forms\Components\Card::make('Packages Information')
                    ->schema([

                        Forms\Components\Grid::make(2)->schema([

                            Forms\Components\Select::make('truck_id')
                                ->label('Truck Type')
                                ->reactive()
                                ->options(function (callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $user_id = $get('user_id');
                                    if ($fromArea && $toArea) {
                                        return Destination::where('user_id', $user_id)
                                            ->where('from_area', $fromArea)
                                            ->where('to_area', $toArea)
                                            ->whereNotNull('price')
                                            ->with('truck')
                                            ->get()
                                            ->pluck('truck.name', 'truck.id')
                                            ->toArray();
                                    }

                                    return [];
                                })
                                ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $truck = $get('truck_id');
                                    $user_id = $get('user_id');
                                    if ($fromArea && $toArea && $truck && $user_id) {
                                        $destination = Destination::where('user_id', $user_id)
                                            ->where('from_area', $fromArea)
                                            ->where('to_area', $toArea)
                                            ->where('truck_id', $truck)
                                            ->whereNotNull('price')
                                            ->first();
                                        if ($destination) {
                                            $set('total_price', $destination->price);
                                        }
                                    }
                                })
                                ->required(),
                        ]),

                        Forms\Components\Repeater::make('packages')
                            ->relationship('packages')
                            ->defaultItems(1)
                            ->columns(2)
                            ->minItems(1)
                            ->addActionLabel('Add New Package')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Package Type')
                                    ->reactive()
                                    ->options(['Box' => 'Box', 'Pallets' => 'Pallets', 'Other' => 'Other'])
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                            $truckWeight = Truck::find($get('../../truck_id'))->weight ?? 0;
                                            $totalPackageWeight = collect($get('../../packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                            $availableWeight = $truckWeight - $totalPackageWeight;

                                            if ($availableWeight < 0) {
                                                Notification::make()
                                                    ->title('Truck Weight is not enough')
                                                    ->danger()
                                                    ->send();
                                                $fail('');
                                            }
                                        },
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->reactive()
                                    ->hidden(fn ($get) => $get('type') != 'Other')
                                    ->required(fn ($get) => $get('type') == 'Other')
                                    ->dehydrated(fn ($get) => $get('type') == 'Other'),

                                Forms\Components\TextInput::make('weight')
                                    ->label('Weight')
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('package_weight', ($get('weight') ?: 0) * ($get('quantity') ?: 0))),

                                Forms\Components\TextInput::make('length')
                                    ->label('Length')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('width')
                                    ->label('Width')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('height')
                                    ->label('Height')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->required()
                                    ->integer()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('package_weight', ($get('weight') ?: 0) * ($get('quantity') ?: 0))),

                                Forms\Components\TextInput::make('note')
                                    ->label('Note'),

                                Forms\Components\Placeholder::make('package_weight')
                                    ->label('Package Weight')
                                    ->content(fn ($get) => new HtmlString('<h1>'.(($get('weight') ?: 0) * ($get('quantity') ?: 0)).'</h1>')),
                            ])->afterStateUpdated(function ($state, $set, $get) {
                                $truckWeight = Truck::find($get('truck_id'))->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;
                                $set('available_weight', $availableWeight);
                            }),

                        Forms\Components\Placeholder::make('total_weight')
                            ->label('Total Weight of All Packages')
                            ->content(function ($get) {
                                $totalWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));

                                return new HtmlString('<h1>'.$totalWeight.'</h1>');
                            }),

                        Forms\Components\Placeholder::make('available_weight')
                            ->label('Available Weight in Selected Truck')
                            ->content(function ($get) {
                                $truckWeight = Truck::find($get('truck_id'))->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;

                                return new HtmlString('<h1>'.$availableWeight.'</h1>');
                            }),

                        Forms\Components\Placeholder::make('total_price')
                            ->label('Total Price')
                            ->content(function ($get) {
                                $fromArea = $get('from_area');
                                $toArea = $get('to_area');
                                $truck = $get('truck_id');
                                $user_id = $get('user_id');
                                if ($fromArea && $toArea && $truck && $user_id) {
                                    $destination = Destination::where('user_id', $user_id)
                                        ->where('from_area', $fromArea)
                                        ->where('to_area', $toArea)
                                        ->where('truck_id', $truck)
                                        ->whereNotNull('price')
                                        ->first();

                                    return new HtmlString('<h1>'.$destination->price.'</h1>');
                                }

                                return new HtmlString('<h1>'. 0 .'</h1>');

                            }),

                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('fromCity.name')->label('From City'),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toCity.name')->label('To City'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {

                        $destination = $this->getOwnerRecord()->destinations()
                            ->whereNotNull('price')
                            ->where('from_area', $data['from_area'])
                            ->where('to_area', $data['to_area'])
                            ->first();

                        if ($destination) {
                            $data['price'] = $destination->price;
                        } else {
                            return redirect()->back()->with('error', 'Destination not found');
                        }

                        $data['user_id'] = $this->getOwnerRecord()->id;

                        return $model::create($data);
                    })
                    ->url(ShipmentResource::getUrl('create', ['user' => $this->getOwnerRecord()->id]))
                    ->successRedirectUrl(fn (Model $record): string => route('/')),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Model $record): string => ShipmentResource::getUrl('edit', ['record' => $record])),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->hasRole('Client');
    }
}
