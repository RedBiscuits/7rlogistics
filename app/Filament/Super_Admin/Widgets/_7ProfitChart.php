<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class _7ProfitChart extends ChartWidget
{
    protected static ?string $heading = 'Profit over last 12 months';

    protected static ?int $sort = 7;

    protected function getData(): array
    {
        $data = Trend::query(Shipment::query()->whereNot('status', 'Cancelled'))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('price');

        return [
            'datasets' => [
                [
                    'label' => 'Profit earned',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'Total Profit earned over last 12 months.';
    }

    protected static function getColumns(): int
    {
        return 12;  // Full width
    }
}
