<?php
namespace App\Filament\Finance\Widgets;

use App\Models\Claim;
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
        $stats = Claim::selectRaw('
            COUNT(*) as total_claims,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_claims,
            SUM(CASE WHEN status != 0 THEN 1 ELSE 0 END) as completed_claims,
            (SELECT SUM(cp.price) FROM claims_packages cp) as total_money,
            (SELECT SUM(cp.price) FROM claims_packages cp JOIN claims c ON cp.claim_id = c.id WHERE c.status = 0) as pending_money,
            (SELECT SUM(cp.price) FROM claims_packages cp JOIN claims c ON cp.claim_id = c.id WHERE c.status != 0) as completed_money
        ')->first();

        return [
            Stat::make('Claims Money', $stats->total_money ?? 0)
                ->description($stats->total_claims . ' total claims')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/claims')
                ->color('danger'),

            Stat::make('Awaiting Collection Claims', $stats->pending_money ?? 0)
                ->description($stats->pending_claims . ' awaiting collection claims')
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger')
                ->url('finance/claims?tableFilters[status][value]=pending'),

            Stat::make('Collected Claims Money', $stats->completed_money ?? 0)
                ->description($stats->completed_claims . ' completed claims')
                ->icon('heroicon-o-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('finance/claims?tableFilters[status][value]=completed&tableFilters[status][values][0]=1')
                ->color(Color::Indigo),
        ];
    }
}
