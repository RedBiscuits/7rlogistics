<?php

namespace App\Filament\Operations\Resources\CompanyResource\RelationManagers;

// Models
use App\Filament\Operations\Resources\ShipmentResource;
use App\Models\Area;
use App\Models\Destination;
// Filament & Forms
use App\Models\TruckType;
use App\Models\User;
use Closure;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
// Laravel
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
// Third Party
use Filament\Tables;
use Filament\Tables\Table;
// Resources
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ShipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(function (callable $get) {
                        $company_id = $this->getOwnerRecord()->id;
                        if (! $company_id) {
                            return [];
                        }

                        return User::where('company_id', $company_id)->pluck('name', 'id');
                    }),

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
                                Forms\Components\Select::make('from_area')
                                    ->label('From Area')
                                    ->reactive()
                                    ->options(function (Get $get) {
                                        $company_id = $this->getOwnerRecord()->id;

                                        $dests = Destination::where('company_id', $company_id)
                                            ->whereNotNull('price')
                                            ->with(['fromArea', 'toArea'])
                                            ->get();

                                        $areas = $dests->map(function ($dest) {
                                            return [$dest->from_area, $dest->to_area];
                                        })->flatten()->unique()->values();

                                        return Area::whereIn('id', $areas)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $set('price', null);
                                        $set('truck_type_id', null);
                                        $set('to_area', null);
                                    })
                                    ->required(),

                                Forms\Components\Select::make('to_area')
                                    ->label('To Area')
                                    ->options(function (callable $get, callable $set) {
                                        $company_id = $this->getOwnerRecord()->id;
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
                                        $set('truck_type_id', null);
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

                            Forms\Components\Select::make('truck_type_id')
                                ->label('Truck Type')
                                ->reactive()
                                ->options(function (callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $company_id = $this->getOwnerRecord()->id;
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
                                            ->pluck('truck.name', 'truck.id')
                                            ->toArray();
                                    }

                                    return [];
                                })
                                ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                    $fromArea = $get('from_area');
                                    $toArea = $get('to_area');
                                    $truck = $get('truck_type_id');
                                    $company_id = $this->getOwnerRecord()->id;
                                    if ($fromArea && $toArea && $truck && $company_id) {
                                        $destination = Destination::where('company_id', $company_id)
                                            ->where('from_area', $fromArea)
                                            ->where('to_area', $toArea)
                                            ->where('truck_type_id', $truck)
                                            ->whereNotNull('price');
                                        \Log::info($destination->toSql());

                                        $destination = $destination->first();
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

                                            $truckWeight = TruckType::find($get('../../truck_type_id'))->weight ?? 0;
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
                                $truckWeight = TruckType::find($get('truck_type_id'))->weight ?? 0;
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
                                $truckWeight = TruckType::find($get('truck_type_id'))->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;

                                return new HtmlString('<h1>'.$availableWeight.'</h1>');
                            }),

                        Forms\Components\Placeholder::make('total_price')
                            ->label('Total Price')
                            ->content(function ($get) {
                                $fromArea = $get('from_area');
                                $toArea = $get('to_area');
                                $truck = $get('truck_type_id');
                                $company_id = $this->getOwnerRecord()->id;
                                if ($fromArea && $toArea && $truck && $company_id) {
                                    $destination = Destination::where('company_id', $company_id)
                                        ->where(function ($query) use ($fromArea, $toArea) {
                                            $query->where(function ($q) use ($fromArea, $toArea) {
                                                $q->where('from_area', $fromArea)
                                                    ->where('to_area', $toArea);
                                            })->orWhere(function ($q) use ($fromArea, $toArea) {
                                                $q->where('to_area', $fromArea)
                                                    ->where('from_area', $toArea);
                                            });
                                        })
                                        ->where('truck_type_id', $truck)
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
                Tables\Columns\TextColumn::make('user.name')->label('User')
                    ->getStateUsing(function ($record) {
                        if (! $record->user) {
                            return 'Not assigned';
                        }

                        return $record->user->position.' - '.$record->user->name;
                    }),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('truck.name')->label('Truck')
                    ->getStateUsing(fn ($record) => $record->truck->name.' - ('.$record->truck->plate_number.')'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
                Tables\Columns\TextColumn::make('created_at')->label('Creation Date')->dateTime('Y-m-d H:i A'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $destination = $this->getOwnerRecord()->destinations()
                            ->whereNotNull('price')
                            ->where(function ($query) use ($data) {
                                $query->where(function ($q) use ($data) {
                                    $q->where('from_area', $data['from_area'])
                                        ->where('to_area', $data['to_area']);
                                })->orWhere(function ($q) use ($data) {
                                    $q->where('to_area', $data['from_area'])
                                        ->where('from_area', $data['to_area']);
                                });
                            })
                            ->where('truck_type_id', $data['truck_type_id'])
                            ->first();

                        if (! $destination) {
                            throw new \Exception('Destination not found');
                        }

                        $data['price'] = $destination->price;
                        $data['company_id'] = $this->getOwnerRecord()->id;

                        return $model::create($data);
                    })
                    ->successRedirectUrl(fn (Model $record): string => ShipmentResource::getUrl('index')),

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
}
