<?php

namespace App\Filament\Operations\Resources;

use App\Components\Shipment\ClaimInformationComponent;
use App\Enums\ShipmentStatus;
use App\Filament\Operations\Resources\ShipmentResource\Pages;
use App\Models\Area;
use App\Models\Company;
use App\Models\Destination;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\Truck;
use App\Models\User;
use App\Utils\AcceptedMedia;
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
use Illuminate\Support\HtmlString;

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
                    ->label('Company')
                    ->options(Company::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set) {
                        $set('user_id', null);
                    }),

                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(function (callable $get) {
                        $company_id = $get('company_id');
                        if (! $company_id) {
                            return [];
                        }

                        return User::where('company_id', $company_id)->pluck('name', 'id');
                    })
                    ->disabled(fn ($get) => ! $get('company_id')),

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
                    ->required(fn ($get) => $get('status') != 0 && $get('status') != 1 && $get('status') != 6)
                    ->dehydrated(fn ($get) => $get('status') != 0 && $get('status') != 1)
                    ->helperText(function (callable $get) {
                        $driverId = $get('driver_id');
                        if ($driverId) {
                            $driver = Driver::find($driverId);
                            if ($driver && $driver->isLicenseWarning()) {
                                $days = $driver->getLicenseExpiryDays();
                                return "⚠️ Driver license expires in {$days} days";
                            }
                            if ($driver && $driver->isLicenseBlocked()) {
                                $days = $driver->getLicenseExpiryDays();
                                return "🚫 Driver license expires in {$days} days - Actions blocked";
                            }
                        }
                        return null;
                    })
                    ->afterStateUpdated(function (callable $get, callable $set, $state) {
                        if ($state) {
                            $driver = Driver::find($state);
                            if ($driver && $driver->isLicenseBlocked()) {
                                Notification::make()
                                    ->title('License Expiry Warning')
                                    ->body("Driver license expires in {$driver->getLicenseExpiryDays()} days. Actions are blocked.")
                                    ->warning()
                                    ->send();
                            } elseif ($driver && $driver->isLicenseWarning()) {
                                Notification::make()
                                    ->title('License Expiry Warning')
                                    ->body("Driver license expires in {$driver->getLicenseExpiryDays()} days.")
                                    ->warning()
                                    ->send();
                            }
                        }
                    }),

                ClaimInformationComponent::render(),

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
                                    ->required(),

                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Creation Date')
                                    ->default(now())
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
                                        $company_id = $get('company_id');

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
                                        $set('truck_id', null);
                                        $set('to_area', null);
                                    })
                                    ->required(),

                                Forms\Components\Select::make('to_area')
                                    ->label('To Area')
                                    ->options(function (callable $get, callable $set) {
                                        $company_id = $get('company_id');
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
                                        $set('truck_id', null);
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('from_location')
                                    ->label('From Location')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('to_location')
                                    ->label('To Location')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

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

                                Forms\Components\Select::make('truck_id')
                                    ->label('Assigned Truck')
                                    ->reactive()
                                    ->helperText(function (callable $get, $state, $record) {
                                        if ($record) {
                                            return 'User requested a truck type: '.$record->requested_truck;
                                        }

                                        return 'Assign a specific truck';
                                    })
                                    ->options(function (callable $get, callable $set) {
                                        $fromArea = $get('from_area');
                                        $toArea = $get('to_area');
                                        $company_id = $get('company_id');
                                        if ($fromArea && $toArea) {
                                            return Truck::select('trucks.id', 'trucks.name', 'trucks.plate_number')
                                                ->join('truck_types', 'trucks.truck_type_id', '=', 'truck_types.id')
                                                ->join('destinations', 'truck_types.id', '=', 'destinations.truck_type_id')
                                                ->where(function ($query) use ($fromArea, $toArea) {
                                                    $query->where(function ($q) use ($fromArea, $toArea) {
                                                        $q->where('destinations.from_area', $fromArea)
                                                            ->where('destinations.to_area', $toArea);
                                                    })->orWhere(function ($q) use ($fromArea, $toArea) {
                                                        $q->where('destinations.from_area', $toArea)
                                                            ->where('destinations.to_area', $fromArea);
                                                    });
                                                })
                                                ->where('destinations.company_id', $company_id)
                                                ->get()
                                                ->mapWithKeys(function ($truck) {
                                                    return [$truck->id => $truck->name.' - '.$truck->plate_number];
                                                });
                                        }

                                        return [];
                                    })
                                    ->afterStateUpdated(function (Forms\Components\Select $component, $state, callable $get, callable $set) {
                                        $fromArea = $get('from_area');
                                        $toArea = $get('to_area');
                                        $truck_id = $get('truck_id');
                                        $company_id = $get('company_id');

                                        if ($fromArea && $toArea && $truck_id && $company_id) {
                                            $destination = Destination::where('company_id', $company_id)
                                                ->where('from_area', $fromArea)
                                                ->where('to_area', $toArea)
                                                ->where('truck_type_id', Truck::find($truck_id)->truckType->id)
                                                ->whereNotNull('price');

                                            $destination = $destination->first();
                                            if ($destination) {
                                                $set('price', $destination->price);
                                            }
                                        }
                                    })
                                    ->required(),

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
                                    ->debounce(1000)
                                    ->reactive()
                                    ->required()
                                    ->numeric()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state !== null) {
                                            $weight = floatval($state);
                                            $quantity = floatval($get('quantity') ?: 0);
                                            $set('package_weight', $weight * $quantity);

                                            $truckWeight = Truck::find($get('../../truck_id'))->truckType->weight ?? 0;
                                            $totalPackageWeight = collect($get('../../packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                            $availableWeight = $truckWeight - $totalPackageWeight;

                                            if ($availableWeight < 0) {
                                                Notification::make()
                                                    ->title('Truck Weight is not enough')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }
                                    }),

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
                                    ->integer()
                                    ->required()
                                    ->debounce(500)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state !== null) {
                                            $quantity = floatval($state);
                                            $weight = floatval($get('weight') ?: 0);
                                            $set('package_weight', $weight * $quantity);

                                            $truckWeight = Truck::find($get('truck_id'))->truckType->weight ?? 0;
                                            $totalPackageWeight = collect($get('packages') ?: [])->sum(
                                                fn ($package) => (floatval($package['weight'] ?: 0) * floatval($package['quantity'] ?: 0))
                                            );
                                            $availableWeight = $truckWeight - $totalPackageWeight;

                                            if ($availableWeight < 0) {
                                                Notification::make()
                                                    ->title('Truck Weight is not enough')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('note')
                                    ->label('Note'),

                                SpatieMediaLibraryFileUpload::make('attachment')
                                    ->collection('attachment')
                                    ->label('Attachment')
                                    ->downloadable()
                                    ->openable()
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->previewable(true)
                                    ->acceptedFileTypes(AcceptedMedia::getAcceptedMediaTypes()),

                                Forms\Components\Placeholder::make('package_weight')
                                    ->label('Package Weight')
                                    ->content(fn ($get) => new HtmlString('<h1>'.(($get('weight') ?: 0) * ($get('quantity') ?: 0)).'</h1>')),
                            ])->afterStateUpdated(function ($state, $set, $get) {
                                $truckWeight = Truck::find($get('truck_id'))->truckType->weight ?? 0;
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
                                $truckWeight = Truck::find($get('truck_id'))->truckType->weight ?? 0;
                                $totalPackageWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['weight'] ?: 0) * ($package['quantity'] ?: 0));
                                $availableWeight = $truckWeight - $totalPackageWeight;

                                return new HtmlString('<h1>'.$availableWeight.'</h1>');
                            }),

                        Forms\Components\TextInput::make('price')
                            ->label('Total Price')
                            ->numeric()
                            ->reactive()
                            ->required(),

                        // ->content(function ($get) {
                        //     $fromArea = $get('from_area');
                        //     $toArea = $get('to_area');
                        //     $truck = $get('truck_id');
                        //     $company_id = $get('company_id');
                        //     if ($fromArea && $toArea && $truck && $company_id) {
                        //         $destination = Destination::where('company_id', $company_id)
                        //             ->where(function ($query) use ($fromArea, $toArea) {
                        //                 $query->where(function ($q) use ($fromArea, $toArea) {
                        //                     $q->where('from_area', $fromArea)
                        //                         ->where('to_area', $toArea);
                        //                 })->orWhere(function ($q) use ($fromArea, $toArea) {
                        //                     $q->where('to_area', $fromArea)
                        //                         ->where('from_area', $toArea);
                        //                 });
                        //             })
                        //             ->where('truck_id', $truck)
                        //             ->whereNotNull('price')
                        //             ->first();

                        //         return new HtmlString('<h1>' . $destination->price . '</h1>');
                        //     }

                        //     return new HtmlString('<h1>' . 0 . '</h1>');
                        // }),

                    ]),

                SpatieMediaLibraryFileUpload::make('attachment')
                    ->collection('attachment')
                    ->label('Attatchment')
                    ->downloadable()
                    ->openable()
                    ->multiple()
                    ->maxFiles(5)
                    ->previewable(false),

                Forms\Components\Section::make('Delivery Proofs')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('delivery_proofs')
                            ->collection('delivery_proofs')
                            ->label('Delivery Proof Images')
                            ->downloadable()
                            ->openable()
                            ->multiple()
                            ->maxFiles(10)
                            ->previewable()
                            ->helperText('Upload delivery proof images (minimum 2 required for DELIVERED status)'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record && $record->exists),

                Forms\Components\Section::make('Mobile App Photos')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('pickup_photos')
                            ->collection('pickup_photos')
                            ->label('Pickup Photos')
                            ->downloadable()
                            ->openable()
                            ->multiple()
                            ->maxFiles(10)
                            ->previewable()
                            ->image()
                            ->helperText('Photos uploaded via mobile app when picking up shipment'),

                        SpatieMediaLibraryFileUpload::make('delivery_photos')
                            ->collection('delivery_photos')
                            ->label('Delivery Photos')
                            ->downloadable()
                            ->openable()
                            ->multiple()
                            ->maxFiles(10)
                            ->previewable()
                            ->image()
                            ->helperText('Photos uploaded via mobile app when delivering shipment'),

                        SpatieMediaLibraryFileUpload::make('toll_photos')
                            ->collection('toll_photos')
                            ->label('Toll Photos')
                            ->downloadable()
                            ->openable()
                            ->multiple()
                            ->maxFiles(10)
                            ->previewable()
                            ->image()
                            ->helperText('Photos uploaded via mobile app for toll receipts'),

                        SpatieMediaLibraryFileUpload::make('signature')
                            ->collection('signature')
                            ->label('Delivery Signature')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->image()
                            ->helperText('Signature captured via mobile app upon delivery'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record && $record->exists),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => 
                $query->with([
                    'company',
                    'fromArea',
                    'toArea',
                    'truck.truckType',
                    'driver',
                    'user'
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Code')->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Company')->sortable(),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To'),
                Tables\Columns\TextColumn::make('truck')->label('Truck')
                    ->getStateUsing(fn ($record) => $record->truck ? $record->truck->name.' - ('.$record->truck->plate_number.')' : ''),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('driver_acknowledged_at')
                    ->label('Acknowledged')
                    ->dateTime('Y-m-d H:i')
                    ->badge()
                    ->color(fn ($record) => $record->driver_acknowledged_at ? 'success' : 'danger')
                    ->icon(fn ($record) => $record->driver_acknowledged_at ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver_on_way_to_collect_at')
                    ->label('On Way to Collect')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('awaiting_collection_at')
                    ->label('Awaiting Collection')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
                Tables\Columns\TextColumn::make('created_at')->label('Creation Date')->dateTime('Y-m-d H:i A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')
                    ->options(ShipmentStatus::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('fromArea')
                    ->relationship('fromArea', 'name'),
                Tables\Filters\SelectFilter::make('toArea')
                    ->relationship('toArea', 'name'),
                Tables\Filters\SelectFilter::make('truck')
                    ->relationship('truck', 'plate_number'),
                Tables\Filters\SelectFilter::make('driver')
                    ->relationship('driver', 'name'),
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),

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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
