<?php

namespace App\Filament\Operations\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _6ShipmentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        return [
            Stat::make('Pending Shipments', Shipment::where('status', 0)->count())
                ->description('Shipments submitted by users')
                ->icon('heroicon-o-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=0')
                ->color('PENDING'),

            Stat::make('Approved Shipments', Shipment::where('status', 6)->count())
                ->description('Shipments accepted by personnel')
                ->icon('heroicon-o-arrow-down-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=6')
                ->color('success'),

            Stat::make('Canceled Shipments', Shipment::where('status', 1)->count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-x-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=1')
                ->color('CANCELLED'),

        ];
    }
}
