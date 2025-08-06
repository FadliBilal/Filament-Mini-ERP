<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestSalesTable;
use App\Filament\Widgets\LowStockProductsTable;
use App\Filament\Widgets\PurchasesChart;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            SalesChart::class,
            PurchasesChart::class,
            LatestSalesTable::class,
            LowStockProductsTable::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string
    {
        return 'Dashboard Analitik';
    }
}