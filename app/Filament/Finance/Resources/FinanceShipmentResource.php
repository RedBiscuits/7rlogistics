<?php
namespace App\Filament\Finance\Resources;

use App\Enums\ShipmentStatus;
use App\Filament\Finance\Resources\ClaimsResource;
use App\Filament\Finance\Resources\InvoicesResource;
use App\Filament\Finance\Resources\ShipmentResource\ListShipments;
use App\Models\Shipment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Freight management';

    public static ?string $label = 'Finance Shipment';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Company')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable()->getStateUsing(function (Shipment $record) {
                    return $record->user->name ?? 'N/A';
                }),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
                Tables\Columns\IconColumn::make('has_invoice')
                    ->label('Has Invoice')
                    ->boolean()
                    ->getStateUsing(function (Shipment $record) {
                        return $record->invoice()->exists();
                    }),
                Tables\Columns\IconColumn::make('has_claim')
                    ->label('Has Claim')
                    ->boolean()
                    ->getStateUsing(function (Shipment $record) {
                        return $record->claim()->exists();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invoice_status')
                    ->label('Invoice Status')
                    ->options([
                        'with_invoice'    => 'Has Invoice',
                        'without_invoice' => 'No Invoice',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'with_invoice'    => $query->whereHas('invoice'),
                            'without_invoice' => $query->whereDoesntHave('invoice'),
                            default           => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('claim_status')
                    ->label('Claim Status')
                    ->options([
                        'with_claim'    => 'Has Claim',
                        'without_claim' => 'No Claim',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'with_claim'    => $query->whereHas('claim'),
                            'without_claim' => $query->whereDoesntHave('claim'),
                            default         => $query,
                        };
                    }),
            ])
            ->actions([
                Action::make('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(function (Shipment $record) {
                        $invoice = $record->invoice;
                        if ($invoice) {
                            return InvoicesResource::getUrl('edit', ['record' => $invoice->id]);
                        }
                        return InvoicesResource::getUrl('create', ['shipment_id' => $record->id]);
                    })
                    ->openUrlInNewTab(),
                // Tables\Actions\ViewAction::make(), // Removed/disabled view action
                Action::make('Claim')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(function (Shipment $record) {
                        $claim = $record->claim;
                        if ($claim) {
                            return ClaimsResource::getUrl('edit', ['record' => $claim->id]);
                        }
                        return ClaimsResource::getUrl('create', ['shipment_id' => $record->id]);
                    })
                    ->openUrlInNewTab(),

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
            'index' => ListShipments::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', ShipmentStatus::DELIVERED);
    }
}
