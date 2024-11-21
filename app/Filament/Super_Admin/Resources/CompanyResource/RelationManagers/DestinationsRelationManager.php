<?php

namespace App\Filament\Super_Admin\Resources\CompanyResource\RelationManagers;

use App\Imports\DestinationImport;
use App\Models\Area;
use App\Models\City;
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
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('City Name')
                            ->required(),
                    ])
 
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
                    ->createOptionForm([
                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                return City::pluck('name', 'id')->toArray();
                            })
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Area Name')
                            ->required(),
                    ])

                    ->required(),

                Forms\Components\Select::make('to_city')
                    ->label('To City')
                    ->options(function (callable $get) {
                        return City::pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('City Name')
                            ->required(),
                    ])

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
                    ->createOptionForm([
                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                return City::pluck('name', 'id')->toArray();
                            })
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Area Name')
                            ->required(),

                    ])

                    ->required(),

                Forms\Components\Select::make('truck_id')
                    ->label('Truck')
                    ->options(function (callable $get) {
                        $trucks = Truck::all()->pluck('name', 'id');

                        return $trucks ?? [];
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('EGP')
                    ->minValue(0),
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
                        Forms\Components\FileUpload::make('attachment'),
                    ])->action(function (array $data) {
                        $file = public_path('storage'.'/'.$data['attachment']);
                        $this->getOwnerRecord()->destinations()->delete();
                        HeadingRowFormatter::default('none');
                        $headings = (new HeadingRowImport)->toArray($file)[0][0];
                        $headings = array_slice($headings, 4);
                        Excel::import(new DestinationImport($this->getOwnerRecord(), $headings), $file, null, \Maatwebsite\Excel\Excel::XLSX);

                        Notification::make()
                            ->title('Destinations Imported Successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $destination = $this->getOwnerRecord()->destinations()
                            ->where('from_city', $data['from_city'])
                            ->where('from_area', $data['from_area'])
                            ->where('to_city', $data['to_city'])
                            ->where('to_area', $data['to_area'])
                            ->where('truck_id', $data['truck_id'])
                            ->first();

                        if ($destination) {
                            return $destination;
                        }

                        $data['company_id'] = $this->getOwnerRecord()->id;

                        return $model::create($data);
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
