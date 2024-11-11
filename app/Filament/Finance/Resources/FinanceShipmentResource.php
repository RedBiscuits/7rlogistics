<?php

namespace App\Filament\Finance\Resources;

use App\Enums\ShipmentStatus;
use App\Filament\Finance\Resources\ShipmentResource\ListShipments;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class FinanceShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Freight management';

    public static ?string $label = 'Finance Shipment';

    protected static ?int $navigationSort = 1;

    // protected static bool $shouldRegisterNavigation = false;

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
                Tables\Columns\TextColumn::make('user.company.name')->label('User')->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Action::make('Claim')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->button()
                    ->action(function (Shipment $record) {
                        // Set the PDF path based on the shipment ID to avoid duplicate creation
                        $pdfPath = 'claims/'.$record->id.'.pdf';

                        // Check if the PDF file already exists in storage
                        if (Storage::exists($pdfPath)) {
                            // If the file exists, download it directly
                            return Storage::download($pdfPath, 'claim_'.$record->id.'.pdf');
                        }

                        // If the file does not exist, generate it
                        $pdf = Pdf::loadView('claim', [
                            'title' => 'Claim',
                            'name' => $record->user->name,
                            'id' => $record->id,
                            'awb' => $record->awb,
                            'claims' => $record->claim?->packages ?? [],
                        ]);

                        // Save the generated PDF file to storage
                        Storage::put($pdfPath, $pdf->output());

                        // Stream the generated PDF file for download
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'claim_'.$record->id.'.pdf');
                    })
                    ->disabled(function (Shipment $record) {
                        return ! $record->claim()->exists();
                    }),
                Action::make('Invoice')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->button()
                    ->action(function (Shipment $record) {
                        $pdfPath = 'invoices/'.$record->id.'.pdf';

                        // Check if the PDF file already exists in storage
                        if (Storage::exists($pdfPath)) {
                            // If the file exists, download it directly
                            return Storage::download($pdfPath, 'invoice_'.$record->id.'.pdf');
                        }

                        // If the file does not exist, generate it
                        $pdf = Pdf::loadView('claim', [
                            'name' => $record->user->name,
                            'id' => $record->id,
                            'awb' => $record->awb,
                            'claims' => [
                                (object) [
                                    'title' => 'Invoice',
                                    'quantity' => 1,
                                    'note' => 'Transportation fee',
                                    'price' => $record->price,
                                ],
                            ],
                        ]);

                        // Save the generated PDF file to storage
                        Storage::put($pdfPath, $pdf->output());

                        // Stream the generated PDF file for download
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'invoice_'.$record->id.'.pdf');
                    })->disabled(function (Shipment $record) {
                        return ! $record->claim()->exists();
                    }),
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
        return parent::getEloquentQuery()->whereDoesntHave('invoice');
    }
}
