<?php

namespace App\Filament\Finance\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class _8ShipmentsChart extends ChartWidget
{
    protected static ?string $heading = 'Shipments over selected year';

    protected static ?int $sort = 4;

    // Enable dropdown filter
    protected static bool $isFilterable = true;

    // Add this method to set the default year
    protected function getDefaultFilterValue(): ?string
    {
        return (now()->year - 2020)->toString(); // Returns current year offset from 2020
    }

    protected function getFilters(): ?array
    {
        $years = [];
        $currentYear = now()->year;

        for ($year = $currentYear; $year >= 2024; $year--) {
            $years[$year] = $year;
        }

        return $years;
    }

    protected function getData(): array
    {
        $query = Shipment::query();
        $year = (int) $this->filter ?: now()->year;

        $query->whereYear('created_at', $year);

        $data = Trend::query($query)
            ->between(
                start: Carbon::create($year, 1, 1),
                end: Carbon::create($year, 12, 31)
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
            'labels' => $data->map(function (TrendValue $value) {
                return Carbon::parse($value->date)->format('F');
            }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'Number of Shipments in '.($this->filter ?: now()->year);
    }
}
