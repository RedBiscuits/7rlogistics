<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Shipment;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _5ShipmentsOperationsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        return [
            Stat::make('Companies', Company::count())
                ->description('Total Number of Companies')
                ->icon('heroicon-o-building-office')
                ->chart([12, 5, 8, 15, 3, 9, 11])
                ->url('admin/companies')
                ->color(Color::Cyan),

            Stat::make('Total Drivers', Driver::count())
                ->description('Total available drivers')
                ->url('admin/drivers')
                ->chart([8, 12, 5, 15, 9, 11, 13])
                ->icon('heroicon-o-truck')
                ->color('DRIVER_RECEIVED'),

            Stat::make('Canceled Shipments', Shipment::where('status', 1)->count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-x-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=1')
                ->color('CANCELLED'),

        ];
    }
}
