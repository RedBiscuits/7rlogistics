<?php

namespace App\Filament\Super_Admin\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class _8ShipmentsChart extends ChartWidget
{
    protected static ?string $heading = 'Shipments';

    protected static ?int $sort = 8;

    protected function getData(): array
    {

        $data = Trend::query(Shipment::query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Shipments created',
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
        return 'Number of Shipments published over last 12 months.';
    }
}
