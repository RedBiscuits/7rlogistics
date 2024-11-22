<?php

namespace App\Filament\Operations\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use App\Models\Company;
use App\Models\Driver;
use App\Models\User;
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
                ->color(Color::Emerald ),

            Stat::make('Companies', Company::count())
                ->description('Total Number of Companies')
                ->icon('heroicon-o-building-office')
                ->chart([12, 5, 8, 15, 3, 9, 11])
                ->url('admin/companies')
                ->color(Color::Cyan),

            Stat::make('Total Drivers', Driver::count())
                ->description('Total available drivers')
                ->url('admin/users?tableFilters[role][value]=2')
                ->chart([8, 12, 5, 15, 9, 11, 13])
                ->icon('heroicon-o-truck')
                ->color('DRIVER_RECEIVED'),
        ];
    }
}
