<?php

namespace App\Filament\Operations\Widgets;

use App\Models\Driver;
use App\Models\Shipment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        $profitOnMonth = Shipment::where('status', 'Delivered')->where('created_at', '>=', now()->subMonth())->sum('price');

        return [
            Stat::make('Total Shipments', Shipment::count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-inbox-stack')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments')
                ->color('success'),

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

            Stat::make('Total Values', Shipment::whereNot('status', 1)->sum('price'))
                ->description('Sum of Money within shipments')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Pending Values', Shipment::whereNotIn('status', [1, 5])->sum('price'))
                ->description('Sum of Money that\s to be collected')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('PENDING'),

            Stat::make('Completed Shipment Values', Shipment::where('status', 5)->sum('price'))
                ->description('Total Money earned')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Completed Shipments', Shipment::where('status', 5)->count())
                ->description('Shipments delivered and quoted')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=5')
                ->color('success'),

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

            Stat::make('Driver Received Shipments', Shipment::where('status', 2)->count())
                ->description('Shipments drivers fetched from clients')
                ->icon('heroicon-o-arrow-right-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=2')
                ->color('ASSIGNEDMISSION'),

            Stat::make('Missions Count', Shipment::whereNotNull('driver_id')->count())
                ->description('Total Missions')
                ->icon('heroicon-o-academic-cap')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/missions')
                ->color('success'),

            Stat::make('Total Customers', User::role('Client')->count())
                ->description('Companies in business')
                ->url('admin/users?tableFilters[role][value]=3')
                ->icon('heroicon-o-user-circle'),

            Stat::make('Total Drivers', Driver::count())
                ->description('Total available drivers')
                ->url('admin/users?tableFilters[role][value]=2')
                ->icon('heroicon-o-truck'),

            Stat::make('Canceled Shipments', Shipment::where('status', 1)->count())
                ->description('Total Number of Shipments')
                ->icon('heroicon-o-x-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('admin/shipments?tableFilters[status][value]=canceled&tableFilters[status][values][0]=1')
                ->color('CANCELLED'),
        ];
    }
}
