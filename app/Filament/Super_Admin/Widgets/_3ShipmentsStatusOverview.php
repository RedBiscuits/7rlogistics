<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _3ShipmentsStatusOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Values', Shipment::whereNot('status', 1)->sum('price'))
                ->description('Sum of Money within shipments')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Pending Values', Shipment::whereNotIn('status', [1, 5])->sum('price'))
                ->description('Sum of Money in progress or awaiting collection')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Completed Shipments Values', Shipment::where('status', 5)->sum('price'))
                ->description('Shipments delivered and quoted')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=completed&tableFilters[status][values][0]=5')
                ->color(Color::Indigo),
        ];
    }
}
