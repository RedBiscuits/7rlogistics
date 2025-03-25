<?php

namespace App\Filament\Client\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class _4ShipmentsChart extends ChartWidget
{
    protected static ?string $heading = 'Shipments over selected year';

    protected static ?int $sort = 5;

    // Enable dropdown filter
    protected static bool $isFilterable = true;

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
        $query = Shipment::query()->where('company_id', auth()->user()->company_id);
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
