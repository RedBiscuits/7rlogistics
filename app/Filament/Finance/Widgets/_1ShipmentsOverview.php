<?php

namespace App\Filament\Finance\Widgets;

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
            Stat::make('Total Invoices', Shipment::count())
                ->description('Total Number of Invoices')
                ->icon('heroicon-o-inbox-stack')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/invoices')
                ->color(Color::Emerald),

            Stat::make('Pending Invoices', Shipment::where('status', 0)->count())
                ->description('Invoices submitted by users')
                ->icon('heroicon-o-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/invoices?tableFilters[status][value]=canceled&tableFilters[status][values][0]=0')
                ->color('PENDING'),

            Stat::make('Completed Invoices', Shipment::where('status', 1)->count())
                ->description('Invoices accepted by personnel')
                ->icon('heroicon-o-arrow-down-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/invoices?tableFilters[status][value]=canceled&tableFilters[status][values][0]=1')
                ->color('success'),

        ];
    }
}
