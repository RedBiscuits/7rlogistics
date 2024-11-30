<?php

namespace App\Filament\Operations\Resources\CompanyResource\RelationManagers;

use App\Imports\DestinationImport;
use App\Models\Area;
use App\Models\City;
use App\Models\Destination;
use App\Models\Truck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class DestinationsRelationManager extends RelationManager
{
    protected static string $relationship = 'destinations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // getOwnerRecord() returns the parent Company model instance that owns this relation
                // In this case, since this is CompanyResource's RelationManager,
                // getOwnerRecord() returns the Company model we're currently viewing
                Forms\Components\Select::make('from_city')
                    ->label('From City')
                    ->options(function (callable $get) {
                        return City::pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(fn (callable $set) => $set('from', null))
                    ->required(),

                Forms\Components\Select::make('from_area')
                    ->label('From Area')
                    ->options(function (callable $get) {
                        $cityId = $get('from_city');
                        if ($cityId) {
                            return Area::where('city_id', $cityId)->pluck('name', 'id') ?? [];
                        }

                        return [];
                    })
                    ->reactive()
                    ->searchable()


                    ->required(),

                Forms\Components\Select::make('to_city')
                    ->label('To City')
                    ->options(function (callable $get) {
                        return City::pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->searchable()


                    ->afterStateUpdated(fn (callable $set) => $set('to', null))
                    ->required(),

                Forms\Components\Select::make('to_area')
                    ->label('To Area')
                    ->options(function (callable $get) {
                        $cityId = $get('to_city');
                        if ($cityId) {
                            return Area::where('city_id', $cityId)->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->reactive()
                    ->searchable()


                    ->required(),

                Forms\Components\Select::make('truck_id')
                    ->label('Truck')
                    ->options(function (callable $get) {
                        $trucks = Truck::all()->pluck('name', 'id');

                        return $trucks ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set, callable $get)  {
                        $destination = Destination::where('company_id', $this->getOwnerRecord()->id)
                            ->where(function ($query) use ($get) {
                                $query->where(function ($q) use ($get) {
                                    $q->where('from_area', $get('from_area'))
                                        ->where('to_area', $get('to_area'));
                                })->orWhere(function ($q) use ($get) {
                                    $q->where('to_area', $get('from_area'))
                                        ->where('from_area', $get('to_area'));
                                });
                            })
                            ->where('truck_id', $get('truck_id'))
                            ->first();
                            
                        $set('price', $destination?->price ?? null);
                    })
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('EGP')
                    ->minValue(0)
                    ->default(function (callable $get) {
                        $destination = $this->getOwnerRecord()->destinations()
                            ->where(function ($query) use ($get) {
                                $query->where(function ($q) use ($get) {
                                    $q->where('from_area', $get('from_area'))
                                        ->where('to_area', $get('to_area'));
                                })->orWhere(function ($q) use ($get) {
                                    $q->where('to_area', $get('from_area'))
                                        ->where('from_area', $get('to_area'));
                                });
                            })
                            ->where('truck_id', $get('truck_id'))
                            ->first();

                        return $destination->price ?? 0;
                    })
                    ->reactive(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('fromCity.name')->label('From City'),
                Tables\Columns\TextColumn::make('fromArea.name')->label('From Area'),
                Tables\Columns\TextColumn::make('toCity.name')->label('To City'),
                Tables\Columns\TextColumn::make('toArea.name')->label('To Area'),
                Tables\Columns\TextColumn::make('truck.name')->label('Truck'),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('EGP'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                Tables\Actions\Action::make('importDestinations')
                    ->label('Import Destinations')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\FileUpload::make('attachment')
                            ->disk('public')
                            ->directory('uploads'),
                    ])->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['attachment']);
                        $this->getOwnerRecord()->destinations()->delete();
                        // Process the Excel file
                        HeadingRowFormatter::default('none');
                        $headings = (new HeadingRowImport)->toArray($filePath)[0][0];
                        $headings = array_slice($headings, 4);

                        Excel::import(new DestinationImport($this->getOwnerRecord(), $headings), $filePath, null, \Maatwebsite\Excel\Excel::XLSX);

                        // Send success notification
                        Notification::make()
                            ->title('Destinations Imported Successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $destination = $this->getOwnerRecord()->destinations()
                            ->where(function ($query) use ($data) {
                                $query->where(function ($q) use ($data) {
                                    $q->where('from_area', $data['from_area'])
                                        ->where('to_area', $data['to_area']);
                                })->orWhere(function ($q) use ($data) {
                                    $q->where('to_area', $data['from_area'])
                                        ->where('from_area', $data['to_area']);
                                });
                            })
                            ->where('truck_id', $data['truck_id'])
                            ->first();

                        if ($destination) {
                            $destination->update(['price' => $data['price']]);

                            Notification::make()
                                ->title('Price Updated Successfully')
                                ->success()
                                ->send();

                            return $destination;
                        }

                        $data['company_id'] = $this->getOwnerRecord()->id;
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
                ->badge($this->getOwnerRecord()->destinations()->count()),

            'Available' => Tab::make()
                ->badge($this->getOwnerRecord()->destinations()->whereNotNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('price')),

            'Pending' => Tab::make()
                ->badge($this->getOwnerRecord()->destinations()->whereNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('price')),
        ];
    }
}
