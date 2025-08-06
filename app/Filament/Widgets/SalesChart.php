<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan (30 Hari Terakhir)';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Trend::model(Sale::class)->between(start: now()->subDays(30), end: now())->perDay()->sum('total_amount');
        return [
            'datasets' => [['label' => 'Total Penjualan', 'data' => $data->map(fn (TrendValue $value) => $value->aggregate)]],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}