<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

use Filament\Support\Colors\Color;

class _1ShipmentsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Shipments', Shipment::count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-inbox-stack')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments')
                ->color(Color::Emerald),

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

        ];
    }
}
