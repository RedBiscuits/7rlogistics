<?php

namespace App\Filament\Finance\Widgets;

use App\Models\Mission;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class _2ShipmentsValuesOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Driver Missions', Mission::count())
                ->description('Missions given to drivers')
                ->icon('heroicon-o-arrow-right-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/missions')
                ->color('ASSIGNEDMISSION'),

            Stat::make('Pending Missions', Mission::where('status', 0)->count())
                ->description('Total Number of Pending Missions')
                ->icon('heroicon-o-arrow-up-tray')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/missions?tableFilters[status][value]=canceled&tableFilters[status][values][0]=0')
                ->color('ON_ITS_WAY'),

            Stat::make('Completed Missions', Mission::where('status', 1)->count())
                ->description('Missions delivered and quoted')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/missions?tableFilters[status][value]=canceled&tableFilters[status][values][0]=1')
                ->color('success'),

        ];
    }
}
