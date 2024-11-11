<?php

namespace App\Filament\Finance\Resources;

use App\Enums\ClaimStatus;
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
                        $ships = Shipment::whereDoesntHave('claim')->get()->pluck('awb','id');

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
                            $price = Shipment::find($get('shipment_id'))->price ?? 0;

                            if ($value > $price) {
                                $fail('Price exceeds shipment price');
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

                                Forms\Components\TextInput::make('note')
                                    ->label('Note')
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
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('shipment_id')->label('TCN')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return "{$record->id} - {$record->creator->name}";
                    }),
                Tables\Columns\TextColumn::make('discount')->label('Discount')->money('EGP'),
                Tables\Columns\TextColumn::make('payment_type')->label('Payment Type')
                    ->sortable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return PaymentType::from($record->status);
                    }),
                Tables\Columns\TextColumn::make('reference')->label('Cheque Reference'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return ClaimStatus::from($record->status);
                    }),
                Tables\Columns\TextColumn::make('packages.price')
                    ->label('Total Price')
                    ->money('EGP')
                    ->getStateUsing(function ($record) {
                        return $record->claimPackages?->sum('price') ?? 0;
                    }),
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
}
