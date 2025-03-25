<?php

namespace App\Filament\Finance\Widgets;

use App\Enums\ClaimStatus;
use App\Enums\PaymentType;
use App\Models\Invoice;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class _4LatestInvoices extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Invoices';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('shipment_id')->label('Shipment')->sortable(),
                Tables\Columns\TextColumn::make('shipment.truck.truckType.name')->label('Truck Type'),
                Tables\Columns\TextColumn::make('shipment.driver.name')->label('Driver'),
                Tables\Columns\TextColumn::make('shipment.fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('shipment.toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('shipment.price')->label('Price')->money('EGP'),
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
                Tables\Columns\TextColumn::make('created_at')->label('Invoice Creation')->dateTime('d-m-Y H:i'),

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
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('EGP')
                    ->sortable()
                    ->badge()
                    ->searchable()
                    ->alignment('right')
                    ->weight('bold')
                    ->color(fn ($record) => $record->price > 10000 ? 'danger' : 'success'),
            ]);
    }
}
