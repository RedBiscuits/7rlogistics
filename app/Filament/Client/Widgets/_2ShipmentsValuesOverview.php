<?php

namespace App\Filament\Client\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class _2ShipmentsValuesOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('On Delivery Shipments', Shipment::where('status', 4)->where('company_id', auth()->user()->company_id)->count())
                ->description('Total Number of On Delivery Shipments')
                ->icon('heroicon-o-arrow-up-tray')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=4')
                ->color('ON_ITS_WAY'),

            Stat::make('Completed Shipments', Shipment::where('status', 5)->where('company_id', auth()->user()->company_id)->count())
                ->description('Shipments delivered and quoted')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=5')
                ->color('success'),

            Stat::make(
                'Total Spent',
                Shipment::where('status', 5)
                    ->where('company_id', auth()->user()->company_id)->sum('price')
            )
                ->description('Money spent with 7R Logistics')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

        ];
    }
}
