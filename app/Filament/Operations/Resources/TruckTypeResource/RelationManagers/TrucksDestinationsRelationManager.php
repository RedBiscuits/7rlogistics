<?php

namespace App\Filament\Operations\Resources\TruckTypeResource\RelationManagers;

use App\Models\Area;
use App\Models\Truck;
use App\Models\TruckDestination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrucksDestinationsRelationManager extends RelationManager
{
    protected static string $relationship = 'trucksDestinations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // getOwnerRecord() returns the parent Company model instance that owns this relation
                // In this case, since this is CompanyResource's RelationManager,
                // getOwnerRecord() returns the Company model we're currently viewing
                Forms\Components\Select::make('from_area')
                    ->label('From Area')
                    ->options(function (callable $get) {
                        return Area::pluck('name', 'id');
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('to_area')
                    ->label('To Area')
                    ->options(function (callable $get) {
                        return Area::pluck('name', 'id');
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),

                // Price is set by Finance/Super Admin; hidden from operations users. DB column is nullable.
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                // Tables\Actions\Action::make('importDestinations')
                //     ->label('Import Destinations')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->color('primary')
                //     ->requiresConfirmation()
                //     ->form([
                //         Forms\Components\FileUpload::make('attachment')
                //             ->label('Excel File')
                //             ->helperText('The excel file should have the following columns: City, Area, #, Truck 1, Truck 2, Truck 3, etc.')
                //             ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                //             ->disk('public')
                //             ->directory('uploads'),
                //     ])->action(function (array $data) {
                //         // Get the correct storage path
                //         $filePath = storage_path('app/public/' . $data['attachment']);

                //         // Debug the file path if needed
                //         // \Log::info('File path: ' . $filePath);

                //         if (!file_exists($filePath)) {
                //             Notification::make()
                //                 ->title('File not found at: ' . $filePath)
                //                 ->danger()
                //                 ->send();
                //             return;
                //         }

                //         try {
                //             $this->getOwnerRecord()->destinations()->delete();

                //             HeadingRowFormatter::default('none');
                //             $headings = (new HeadingRowImport)->toArray($filePath)[0][0];
                //             $headings = array_slice($headings, 4);

                //             Excel::import(
                //                 new DestinationImport($this->getOwnerRecord(), $headings),
                //                 $filePath,
                //                 null,
                //                 \Maatwebsite\Excel\Excel::XLSX
                //             );

                //             Notification::make()
                //                 ->title('Destinations Imported Successfully')
                //                 ->success()
                //                 ->send();
                //         } catch (\Exception $e) {
                //             Notification::make()
                //                 ->title('Import Failed')
                //                 ->danger()
                //                 ->body($e->getMessage())
                //                 ->send();
                //         }
                //     }),

                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $destination = TruckDestination::where(function ($query) use ($data) {
                            $query->where(function ($q) use ($data) {
                                $q->where('from_area', $data['from_area'])
                                    ->where('to_area', $data['to_area']);
                            })->orWhere(function ($q) use ($data) {
                                $q->where('to_area', $data['from_area'])
                                    ->where('from_area', $data['to_area']);
                            });
                        })
                            ->where('truck_type_id', $this->getOwnerRecord()->id)
                            ->first();

                        if ($destination) {
                            return $destination;
                        }

                        $data['truck_type_id'] = $this->getOwnerRecord()->id;
                        $data['price'] = $data['price'] ?? null;

                        return $model::create($data);
                    })
                    ->successNotificationTitle(function ($record) {
                        // Only show success notification if it's a new record
                        return $record->wasRecentlyCreated ? 'Created successfully' : null;
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge($this->getOwnerRecord()->trucksDestinations()->count()),

            'Available' => Tab::make()
                ->badge($this->getOwnerRecord()->trucksDestinations()->whereNotNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('price')),

            'Pending' => Tab::make()
                ->badge($this->getOwnerRecord()->trucksDestinations()->whereNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('price')),
        ];
    }
}
