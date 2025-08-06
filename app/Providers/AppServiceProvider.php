<?php

namespace App\Providers;

use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Observers\PurchaseItemObserver;
use App\Observers\SaleItemObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        PurchaseItem::observe(PurchaseItemObserver::class);
        SaleItem::observe(SaleItemObserver::class);
    }
}