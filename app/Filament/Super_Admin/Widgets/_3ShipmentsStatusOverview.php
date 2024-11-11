<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Colors\Color;

class _3ShipmentsStatusOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Completed Shipments', Shipment::where('status', 5)->count())
                ->description('Shipments delivered and quoted')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=5')
                ->color(Color::Indigo),

            Stat::make('On Delivery Shipments', Shipment::where('status', 4)->count())
                ->description('Total Number of On Delivery Shipments')
                ->icon('heroicon-o-arrow-up-tray')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=4')
                ->color('ON_ITS_WAY'),

            Stat::make('Driver Assigned Shipments (Missions)', Shipment::where('status', 3)->count())
                ->description('Missions given to drivers')
                ->icon('heroicon-o-arrow-right-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=3')
                ->color('ASSIGNEDMISSION'),
        ];
    }
}
