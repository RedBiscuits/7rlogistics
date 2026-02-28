<?php

namespace App\Filament\Operations\Widgets;

use App\Enums\ShipmentStatus;
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
                ->url('operations/companies')
                ->color(Color::Cyan),

            Stat::make('Total Drivers', Driver::count())
                ->description('Total available drivers')
                ->url('operations/drivers')
                ->chart([8, 12, 5, 15, 9, 11, 13])
                ->icon('heroicon-o-truck')
                ->color(Color::Cyan),

            Stat::make('Cancelled Shipments', Shipment::where('status', ShipmentStatus::CANCELLED)->count())
                ->description('Cancelled shipments')
                ->icon('heroicon-o-x-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('operations/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=' . ShipmentStatus::CANCELLED->value)
                ->color(ShipmentStatus::CANCELLED->getColor()),
        ];
    }
}
