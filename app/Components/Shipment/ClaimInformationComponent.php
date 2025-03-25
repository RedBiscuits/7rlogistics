<?php
namespace App\Components\Shipment;

use Filament\Forms\Components\Section;
use App\Enums\ShipmentStatus;
use Filament\Forms;
use Filament\Forms\Set;
use App\Enums\ClaimType;

class ClaimInformationComponent
{
    public static function render(): Section
    {
        return Forms\Components\Section::make('Claim Information')
            ->schema([
                Forms\Components\Toggle::make('has_claim')
                    ->label('Does this shipment have a claim?')
                    ->reactive()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('claim_packages')
                    ->schema([
                        Forms\Components\Select::make('claim_type')
                            ->label('Claim Type')
                            ->options(collect(ClaimType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->getLabel()]))
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('EGP')
                           ,
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->addActionLabel('Add New Claim')
                    ->minItems(1)
                    ->visible(fn($get) => $get('has_claim') && $get('status') === ShipmentStatus::DELIVERED->value)
                    ->reactive()
                    ->debounce(1000)
                    ->afterStateUpdated(function ($state, Set $set) {
                        $totalAmount = collect($state)->sum(fn($item) =>
                            (float)($item['quantity'] ?? 0) * (float)($item['amount'] ?? 0)
                        );
                        $set('total_claim_amount', $totalAmount);
                    }),

                Forms\Components\Placeholder::make('total_claim_amount')
                    ->label('Total Claim Amount')
                    ->content(fn($get) => 'EGP ' . number_format($get('total_claim_amount') ?? 0, 2))
                    ->visible(fn($get) => $get('has_claim') && $get('status') === ShipmentStatus::DELIVERED->value),
            ])
            ->visible(fn($get) => $get('status') === ShipmentStatus::DELIVERED->value)
            ->collapsible();
    }
}
