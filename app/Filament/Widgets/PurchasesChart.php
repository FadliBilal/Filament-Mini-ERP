<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class PurchasesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pembelian (30 Hari Terakhir)';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Trend::model(Purchase::class)->between(start: now()->subDays(30), end: now())->perDay()->sum('total_amount');
        return [
            'datasets' => [['label' => 'Total Pembelian', 'data' => $data->map(fn (TrendValue $value) => $value->aggregate), 'borderColor' => '#f87171', 'backgroundColor' => '#fca5a5']],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return Auth::user()->isAdmin();
    }
}