<?php

namespace App\Filament\Operations\Resources;

use App\Enums\ShipmentStatus;
use App\Filament\Operations\Resources\ShipmentResource\Pages;
use App\Models\Area;
use App\Models\City;
use App\Models\Company;
use App\Models\Destination;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\Truck;
use App\Models\User;
use Closure;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Freight management';

    protected static ?int $navigationSort = 1;

    // protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('company_id')
                    ->label('Client')
                    ->options(function (callable $get) {
                        return Company::query()
                            ->select(['id', 'name'])
                            ->orderBy('id')
                            ->get()
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('from_city', null);
                        $set('to_city', null);
                        $set('from_area', null);
                        $set('to_area', null);
                    })->default(request('company'))
                    ->required(),

                Forms\Components\ToggleButtons::make('status')
                    ->inline()
                    ->options(ShipmentStatus::class)
                    ->required()
                    ->reactive()
                    ->default(0),

                Forms\Components\Select::make('driver_id')
                    ->label('Driver')
                    ->options(function (callable $get) {
                        $users = Driver::pluck('name', 'id')->toArray();

                        return $users ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->hidden(fn ($get) => $get('status') == 0 || $get('status') == 1)
                    ->required(fn ($get) => $get('status') != 0 && $get('status') != 1)
                    ->dehydrated(fn ($get) => $get('status') != 0 && $get('status') != 1),

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

                                Forms\Components\DateTimePicker::make('pickup_date')
                                    ->label('Pickup Date')
                                    ->date()
                                    ->required(),

                                Forms\Components\TextInput::make('awb')
                                    ->label('AWB Number')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Card::make('Destination Information')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([

                                Forms\Components\Select::make('from_city')
                                    ->label('From City')
                                    ->options(function (callable $get) {
                                        $get('company_id') ? $cites = City::where('company_id', $get('company_id'))->pluck('name', 'id') : $cites = [];

                                        return $cites ?? [];
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('City Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data, Get $get) {
                                        if ($get('company_id')) {
                                            $data['company_id'] = $get('company_id');

                                            return City::create($data)->id;
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('from', null))
                                    ->required(),

                                Forms\Components\Select::make('from_area')
                                    ->label('From Area')
                                    ->reactive()
                                    ->options(function (Get $get, callable $set) {
                                        $cityId = $get('from_city');
                                        $company_id = $get('company_id');
                                        if ($cityId && $company_id) {
                                            return Destination::where('company_id', $company_id)
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
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\Select::make('city_id')
                                            ->label('City')
                                            ->reactive()
                                            ->options(function (Get $get, Component $livewire) {
                                                $get('company_id') ? $cites = City::where('company_id', $get('company_id'))->pluck('name', 'id') : $cites = [];

                                                return $cites ?? [];
                                            })
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Area Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data, Get $get) {
                                        return Area::create($data)->id;
                                    })
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $fromArea = $get('from_area');
                                        $toArea = $get('to_area');
                                        $company_id = $get('company_id');
                                        if ($fromArea && $toArea && $company_id) {
                                            $destination = Destination::where('company_id', $company_id)->where('from_area', $fromArea)
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
                                    ->options(function (callable $get) {
                                        $get('company_id') ? $cites = City::where('company_id', $get('company_id'))->pluck('name', 'id') : $cites = [];

                                        return $cites ?? [];
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('City Name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data, Get $get) {
                                        if ($get('company_id')) {
                                            $data['company_id'] = $get('company_id');

                                            return City::create($data)->id;
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('to_area', null))
                                    ->required(),

                                Forms\Components\Select::make('to_area')
                                    ->label('To Area')
                                    ->options(function (callable $get, callable $set) {
                                        $cityId = $get('to_city');
                                        $company_id = $get('company_id');
                                        if ($cityId && $company_id) {
                                            return Destination::where('company_id', $company_id)
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
                                            ->options(function (callable $get) {
                                                $get('company_id') ? $cites = City::where('company_id', $get('company_id'))->pluck('name', 'id') : $cites = [];

                                                return $cites ?? [];
                                            })
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
                                        $company_id = $get('company_id');
                                        if ($fromArea && $toArea && $company_id) {
                                            $destination = Destination::where('company_id', $company_id)->where('from_area', $fromArea)
                                                ->where('to_area', $toArea)
                                                ->whereNotNull('price')
                                                ->first();
                                            if ($destination) {
                                                $set('price', $destination->price);
                                            }
                                        }
                                    })
                                    ->required(),

                                Map::make('location')
                                    ->label('Location')
                                    // ->columnSpanFull()
                                    ->default([
                                        'lat' => 40.4168,
                                        'lng' => -3.7038,
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                                        $set('latitude', $state['lat']);
                                        $set('longitude', $state['lng']);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        $set('location', ['lat' => $record?->latitude, 'lng' => $record?->longitude]);
                                    })
                                    ->extraStyles([
                                        'min-height: 300px',
                                        'max-height: 500px',
                                        'width: 100%', // or a specific value like '300px'
                                        'border-radius: 50px',
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
                                    ->extraTileControl([])
                                    ->extraControl([
                                        'zoomDelta' => 1,
                                        'zoomSnap' => 2,
                                    ]),

                                // Forms\Components\TextInput::make('price')
                                //     ->label('Price')
                                //     ->reactive()
                                //     ->required()
                                //     ->readOnly()
                                //     ->disabled(),

                                Forms\Components\TextInput::make('latitude')
                                    ->hiddenLabel()
                                    ->hidden(),

                                Forms\Components\TextInput::make('longitude')
                                    ->hiddenLabel()
                                    ->hidden(),
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
                                    $company_id = $get('company_id');
                                    if ($fromArea && $toArea) {
                                        return Destination::where('company_id', $company_id)
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
                                    $company_id = $get('company_id');
                                    if ($fromArea && $toArea && $truck && $company_id) {
                                        $destination = Destination::where('company_id', $company_id)
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
                                    ->required(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->reactive()
                                    ->required(fn ($get) => $get('type') == 'Other'),

                                Forms\Components\TextInput::make('weight')
                                    ->label('Weight')
                                    ->reactive()
                                    ->required()
                                    ->numeric()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('package_weight', ($get('weight') ?: 0) * ($get('quantity') ?: 0)))
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                            $truckWeight = Truck::find($get('../../truck_id'))->weight ?? 0;
                                            $totalPackageWeight = collect($get('../../packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                            $availableWeight = $truckWeight - $totalPackageWeight;

                                            if ($availableWeight < 0) {
                                                $fail('Truck Weight is not enough');
                                            }
                                        },
                                    ]),

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
                                $company_id = $get('company_id');
                                if ($fromArea && $toArea && $truck && $company_id) {
                                    $destination = Destination::where('company_id', $company_id)
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

                SpatieMediaLibraryFileUpload::make('attachment')
                    ->collection('attachment')
                    ->label('Attatchment')
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
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('fromCity.name')->label('From City'),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toCity.name')->label('To City'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')
                    ->options(ShipmentStatus::class)
                    ->multiple(),

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
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            // 'view' => Pages\ViewShipment::route('/{record}'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
