<?php

namespace App\Filament\Operations\Widgets;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _1ShipmentsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $filterUrl = fn (int $status) => 'operations/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=' . $status;

        return [
            Stat::make('Total Shipments', Shipment::count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-inbox-stack')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('operations/shipments')
                ->color(Color::Emerald),

            Stat::make('Pending Shipments', Shipment::where('status', ShipmentStatus::PENDING)->count())
                ->description('Shipments submitted by users')
                ->icon('heroicon-o-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url($filterUrl(ShipmentStatus::PENDING->value))
                ->color(ShipmentStatus::PENDING->getColor()),

            Stat::make('Approved Shipments', Shipment::where('status', ShipmentStatus::APPROVED)->count())
                ->description('Shipments accepted by personnel')
                ->icon('heroicon-o-arrow-down-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url($filterUrl(ShipmentStatus::APPROVED->value))
                ->color(ShipmentStatus::APPROVED->getColor()),
        ];
    }
}
