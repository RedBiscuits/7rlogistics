<?php

namespace App\Filament\Operations\Widgets;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _2ShipmentsValuesOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $filterUrl = fn (int $status) => 'operations/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=' . $status;

        return [
            Stat::make('Assigned Mission', Shipment::where('status', ShipmentStatus::ASSIGNEDMISSION)->count())
                ->description('Missions assigned to drivers')
                ->icon('heroicon-o-clipboard-document-list')
                ->url($filterUrl(ShipmentStatus::ASSIGNEDMISSION->value))
                ->color(ShipmentStatus::ASSIGNEDMISSION->getColor()),

            Stat::make('Driver Acknowledged', Shipment::where('status', ShipmentStatus::DRIVER_ACKNOWLEDGED)->count())
                ->description('Driver accepted the mission')
                ->icon('heroicon-o-hand-thumb-up')
                ->url($filterUrl(ShipmentStatus::DRIVER_ACKNOWLEDGED->value))
                ->color(ShipmentStatus::DRIVER_ACKNOWLEDGED->getColor()),

            Stat::make('On Way to Collect', Shipment::where('status', ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT)->count())
                ->description('Driver en route to pickup')
                ->icon('heroicon-o-map-pin')
                ->url($filterUrl(ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT->value))
                ->color(ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT->getColor()),

            Stat::make('Awaiting Collection', Shipment::where('status', ShipmentStatus::AWAITING_COLLECTION)->count())
                ->description('At pickup, awaiting loading')
                ->icon('heroicon-o-archive-box')
                ->url($filterUrl(ShipmentStatus::AWAITING_COLLECTION->value))
                ->color(ShipmentStatus::AWAITING_COLLECTION->getColor()),

            Stat::make('Driver Received', Shipment::where('status', ShipmentStatus::DRIVER_RECEIVED)->count())
                ->description('Picked up, in transit to destination')
                ->icon('heroicon-o-arrow-right-circle')
                ->url($filterUrl(ShipmentStatus::DRIVER_RECEIVED->value))
                ->color(ShipmentStatus::DRIVER_RECEIVED->getColor()),

            Stat::make("On It's Way", Shipment::where('status', ShipmentStatus::ON_ITS_WAY)->count())
                ->description('On delivery to destination')
                ->icon('heroicon-o-arrow-up-tray')
                ->url($filterUrl(ShipmentStatus::ON_ITS_WAY->value))
                ->color(ShipmentStatus::ON_ITS_WAY->getColor()),

            Stat::make('Delivered', Shipment::where('status', ShipmentStatus::DELIVERED)->count())
                ->description('Shipments delivered')
                ->icon('heroicon-o-check-circle')
                ->url($filterUrl(ShipmentStatus::DELIVERED->value))
                ->color(ShipmentStatus::DELIVERED->getColor()),
        ];
    }
}
