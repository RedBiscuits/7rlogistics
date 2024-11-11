<?php

namespace App\Filament\Client\Resources\DestinationResource\Pages;

use App\Filament\Client\Resources\DestinationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDestination extends ListRecords
{
    protected static string $resource = DestinationResource::class;

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(auth()->user()->company->destinations()->count()),

            'Available' => Tab::make()
                ->badge(auth()->user()->company->destinations()->whereNotNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('price')),

            'Pending' => Tab::make()
                ->badge(auth()->user()->company->destinations()->whereNull('price')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('price')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
