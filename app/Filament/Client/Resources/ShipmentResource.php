<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ShipmentResource\Pages;
use App\Models\Area;
use App\Models\Shipment;
use App\Models\Truck;
use Closure;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use App\Enums\ShipmentStatus;
use App\Models\Destination;


class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationGroup = 'Freight management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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

                                Forms\Components\DateTimePicker::make('pickup_date')
                                    ->label('Pickup Date')
                                    ->date()
                                    ->required(),

                                Forms\Components\TextInput::make('awb')
                                    ->label('AWB | B/L Number')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Card::make('Destination Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([

                                Forms\Components\Select::make('from_area')
                                    ->label('From Area')
                                    ->options(function (Get $get) {
                                        $company_id = auth()->user()->company_id;

                                        $dests = Destination::where('company_id', $company_id)
                                            ->whereNotNull('price')
                                            ->with(['fromArea', 'toArea'])
                                            ->get();

                                        $areas = $dests->map(function ($dest) {
                                            return [$dest->from_area, $dest->to_area];
                                        })->flatten()->unique()->values();

                                        return Area::whereIn('id', $areas)->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $set('price', null);
                                        $set('requested_truck', null);
                                        $set('to_area', null);
                                    })
                                    ->required(),



                                Forms\Components\Select::make('to_area')
                                    ->label('To Area')
                                    ->options(function (callable $get, callable $set) {
                                        $company_id = auth()->user()->company_id;
                                        $fromArea = $get('from_area');
                                        $dests = Destination::where('company_id', $company_id)
                                            ->whereNotNull('price')
                                            ->get();

                                        $areas = $dests->map(function ($dest) use ($fromArea) {
                                            return $dest->from_area == $fromArea ? $dest->to_area : $dest->from_area;
                                        })
                                            ->unique()
                                            ->values();

                                        return Area::whereIn('id', $areas)->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $set) {
                                        $set('price', null);
                                        $set('requested_truck', null);
                                    })
                                    ->required(),

                                Map::make('location')
                                    ->label('Location')
                                    // ->columnSpanFull()
                                    ->default([
                                        'lat' => 40.4168,
                                        'lng' => -3.7038,
                                    ])
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
                                //     ->disabled()
                            ]),
                    ]),

                Forms\Components\Card::make('Packages Information')
                    ->schema([

                        Forms\Components\Grid::make(2)->schema([

                            Forms\Components\Select::make('requested_truck')
                                ->label('Truck Type')
                                ->reactive()
                                ->options(function (callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $company_id = auth()->user()->company_id;
                                    if ($fromArea && $toArea) {
                                        return Destination::where('company_id', $company_id)
                                            ->where(function ($query) use ($fromArea, $toArea) {
                                                $query->where(function ($q) use ($fromArea, $toArea) {
                                                    $q->where('from_area', $fromArea)
                                                        ->where('to_area', $toArea);
                                                })->orWhere(function ($q) use ($fromArea, $toArea) {
                                                    $q->where('to_area', $fromArea)
                                                        ->where('from_area', $toArea);
                                                });
                                            })
                                            ->whereNotNull('price')
                                            ->with('truck')
                                            ->get()
                                            ->pluck('truck.name', 'truck.name')
                                            ->unique()
                                            ->toArray();
                                    }

                                    return [];
                                })
                                ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $truckName = $get('requested_truck');
                                    $company_id = auth()->user()->company_id;
                                    if ($fromArea && $toArea && $truckName && $company_id) {
                                        $destination = Destination::where('company_id', $company_id)
                                            ->where('from_area', $fromArea)
                                            ->where('to_area', $toArea)
                                            ->whereHas('truck', function ($query) use ($truckName) {
                                                $query->where('name', $truckName);
                                            })
                                            ->whereNotNull('price');
                                        \Log::info($destination->toSql());

                                        $destination = $destination->first();
                                        if ($destination) {
                                            $set('price', $destination->price);
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
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $truckWeight = Truck::where('name', $get('requested_truck'))->first()->weight ?? 0;
                                            $totalPackageWeight = collect($get('packages') ?: [])->sum(fn($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
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
                                    ->hidden(fn($get) => $get('type') != 'Other')
                                    ->required(fn($get) => $get('type') == 'Other')
                                    ->dehydrated(fn($get) => $get('type') == 'Other'),

                                Forms\Components\TextInput::make('weight')
                                    ->label('Weight')
                                    ->numeric()
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(fn($state, $set, $get) => $set('package_weight', ($get('weight') ?: 0) * ($get('quantity') ?: 0)))
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                            $truckWeight = Truck::where('name', $get('requested_truck'))->first()->weight ?? 0;
                                            $totalPackageWeight = collect($get('packages') ?: [])->sum(fn($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
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
                                    ->afterStateUpdated(fn($state, $set, $get) => $set('package_weight', ($get('weight') ?: 0) * ($get('quantity') ?: 0))),

                                Forms\Components\TextInput::make('note')
                                    ->label('Note'),

                                Forms\Components\Placeholder::make('package_weight')
                                    ->label('Package Weight')
                                    ->content(fn($get) => new HtmlString('<h1>' . (($get('weight') ?: 0) * ($get('quantity') ?: 0)) . '</h1>')),
                            ])->afterStateUpdated(function ($state, $set, $get) {

                                $truckWeight = Truck::where('name', $get('requested_truck'))->first()->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;
                                $set('available_weight', $availableWeight);
                            }),

                        Forms\Components\Placeholder::make('total_weight')
                            ->label('Total Weight of All Packages')
                            ->content(function ($get) {
                                $totalWeight = collect($get('packages') ?: [])->sum(fn($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));

                                return new HtmlString('<h1>' . $totalWeight . '</h1>');
                            }),

                        Forms\Components\Placeholder::make('available_weight')
                            ->label('Available Weight in Selected Truck')
                            ->content(function ($get) {
                                $truckWeight = Truck::where('name', $get('requested_truck'))->first()->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;

                                return new HtmlString('<h1>' . $availableWeight . '</h1>');
                            }),

                        Forms\Components\Placeholder::make('total_price')
                            ->label('Total Price')
                            ->content(function ($get) {
                                $fromArea = $get('from_area');
                                $toArea = $get('to_area');
                                $truckName = $get('requested_truck');
                                $company_id = auth()->user()->company_id;
                                // dd($truckName);
                                if ($fromArea && $toArea && $truckName) {
                                    return new HtmlString('<h1>' . Destination::where('company_id', $company_id)
                                        ->where(function ($query) use ($fromArea, $toArea, $truckName) {
                                            $query->where(function ($q) use ($fromArea, $toArea) {
                                                $q->where('from_area', $fromArea)
                                                    ->where('to_area', $toArea);
                                            })->orWhere(function ($q) use ($fromArea, $toArea) {
                                                $q->where('to_area', $fromArea)
                                                    ->where('from_area', $toArea);
                                            })->whereHas('truck', function ($query) use ($truckName) {
                                                $query->where('name', $truckName);
                                            });
                                        })
                                        ->whereNotNull('price')
                                        ->first()->price . '</h1>');
                                }

                                return new HtmlString('<h1>' . 0 . '</h1>');
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
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('user', function ($query) {
                    $query->where('company_id', auth()->user()->company_id);
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Code')->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Company')->sortable(),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To'),
                Tables\Columns\TextColumn::make('requested_truck')
                    ->label('Truck')
                    ->getStateUsing(function ($record) {
                        if ($record->requested_truck) {
                            return $record->requested_truck;
                        }

                        return $record->truck?->name ?? '';
                    }),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
                Tables\Columns\TextColumn::make('created_at')->label('Creation Date')->dateTime('Y-m-d H:i A'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')
                    ->options(ShipmentStatus::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'view' => Pages\ViewShipment::route('/{record}'),
            // 'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
