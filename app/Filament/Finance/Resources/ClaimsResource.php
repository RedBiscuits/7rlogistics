<?php

namespace App\Filament\Finance\Resources;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Enums\PaymentType;
use App\Filament\Finance\Resources\ClaimsResource\CreateClaims;
use App\Filament\Finance\Resources\ClaimsResource\EditClaims;
use App\Filament\Finance\Resources\ClaimsResource\ListClaims;
use App\Models\Claim;
use App\Models\Shipment;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ClaimsResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finance management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('shipment_id')
                    ->label('Shipment')
                    ->options(function (callable $get) {
                        $ships = Shipment::whereDoesntHave('claim')->get()->pluck('id', 'id');

                        return $ships ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('discount', null);
                    })->default(request('user'))
                    ->required(),

                Forms\Components\TextInput::make('discount')
                    ->label('Discount')
                    ->numeric()
                    ->nullable()
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if (Shipment::where('id', $get('shipment_id'))
                                //                  Shipment price      <          Discount
                                ->where('price', '<', $value)
                                ->exists()
                            ) {
                                $fail('Price exceeds shipment price.');
                            }
                        },
                    ]),

                Forms\Components\ToggleButtons::make('status')
                    ->inline()
                    ->options(ClaimStatus::class)
                    ->nullable()
                    ->reactive()
                    ->default(0),

                Forms\Components\ToggleButtons::make('payment_type')
                    ->inline()
                    ->options(PaymentType::class)
                    ->nullable()
                    ->reactive()
                    ->default(0),

                Forms\Components\DateTimePicker::make('collected_at')
                    ->label('Collected at')
                    ->date()
                    ->nullable()
                    ->default(null),

                Forms\Components\TextInput::make('reference')
                    ->label('Cheque reference')
                    ->nullable(),

                Forms\Components\TextInput::make('notes')
                    ->label('Notes')
                    ->nullable(),

                Forms\Components\Card::make('Services Information')
                    ->schema([
                        Forms\Components\Repeater::make('packages')
                            ->relationship('packages')
                            ->defaultItems(1)
                            ->columns(2)
                            ->minItems(1)
                            ->addActionLabel('Add New Service')
                            ->schema([

                                Forms\Components\Select::make('note')
                                    ->label('Note')
                                    ->options(collect(ClaimType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->getLabel()]))
                                    ->reactive()
                                    ->required(),

                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->required()
                                    ->integer()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('total_price', ($get('price') ?: 0) * ($get('quantity') ?: 0))),

                            ]),
                        Forms\Components\Placeholder::make('total_price')
                            ->label('Total Claim Price')
                            ->content(function ($get) {
                                $totalWeight = collect($get('packages') ?: [])->sum(fn ($package) => ($package['price'] ?: 0) * ($package['quantity'] ?: 0));

                                return new HtmlString('<h1>'.$totalWeight.'</h1>');
                            }),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => 
                $query->with([
                    'shipment' => function ($q) {
                        $q->with([
                            'truck.truckType',
                            'driver',
                            'fromArea',
                            'toArea',
                            'company'
                        ]);
                    },
                    'packages',
                    'creator'
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('shipment_id')->label('Shipment')->sortable(),
                Tables\Columns\TextColumn::make('shipment.truck.truckType.name')->label('Truck Type'),
                Tables\Columns\TextColumn::make('shipment.driver.name')->label('Driver'),
                Tables\Columns\TextColumn::make('shipment.fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('shipment.toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('shipment.price')->label('Shipment Price')->money('EGP'),
                Tables\Columns\TextColumn::make('shipment.company.due_days')->label('Due Days')->getStateUsing(function ($record) {
                    $dueDate = $record->created_at->addDays($record->shipment->company->due_days);
                    $diff = number_format($dueDate->diffInDays(now(), false), 0);

                    return ($diff >= 0 ? '+' : '-')
                    .$diff.' days ( '.$dueDate->format('d-m-Y').' )';
                })->badge()->color(fn ($record) => now()->addDays($record->shipment->company->due_days)->isPast() ? 'danger' : 'success'
                ),

                Tables\Columns\TextColumn::make('collected_at')->label('Collected At')
                    ->getStateUsing(function ($record) {
                        return $record->collected_at ? $record->collected_at->format('d-m-Y H:i') : 'Not collected yet';
                    })
                    ->badge()
                    ->color(fn ($record) => $record->collected_at ? 'success' : 'danger')
                    ->icon(fn ($record) => $record->collected_at ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->extraAttributes([
                        'class' => 'px-4 py-2 rounded-full',
                    ]),
                Tables\Columns\TextColumn::make('shipment.created_at')->label('Shipment Creation')->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('created_at')->label('Claim Creation')->dateTime('d-m-Y H:i'),

                Tables\Columns\TextColumn::make('payment_type')->label('Payment Type')
                    ->sortable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return PaymentType::from($record->status);
                    }),
                Tables\Columns\TextColumn::make('packages')
                    ->label('Claim Packages Notes')
                    ->getStateUsing(function ($record) {
                        $notes = $record->packages->pluck('note')
                            ->map(fn ($note) => ClaimType::from($note)->getLabel())
                            ->take(1) // Only take first 2 items
                            ->implode(', ');

                        // Add ellipsis if there are more items
                        if ($record->packages->count() > 2) {
                            $notes .= '...';
                        }

                        return $notes;
                    })
                    ->wrap()
                    ->limit(20), // Limit total character length
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return ClaimStatus::from($record->status);
                    }),
                Tables\Columns\TextColumn::make('packages.sum(price)')
                    ->label('Claim Value')
                    ->money('EGP')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->alignment('right')
                    ->weight('bold')
                    ->color(fn ($record) => $record->packages->sum('price') > 1000 ? 'danger' : 'success'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')
                    ->options(ClaimStatus::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')->label('Status')
                    ->options(PaymentType::class)
                    ->multiple(),
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClaims::route('/'),
            'create' => CreateClaims::route('/create'),
            'edit' => EditClaims::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
