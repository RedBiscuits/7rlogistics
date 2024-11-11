<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use App\Models\User;
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
            Stat::make('Total Values', Shipment::whereNot('status', 1)->sum('price'))
                ->description('Sum of Money within shipments')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Pending Values', Shipment::whereNotIn('status', [1, 5])->sum('price'))
                ->description('Sum of Money that\s to be collected')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('PENDING'),

            Stat::make('Completed Shipment Values', Shipment::where('status', 5)->sum('price'))
                ->description('Total Money earned')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
        ];
    }
}
