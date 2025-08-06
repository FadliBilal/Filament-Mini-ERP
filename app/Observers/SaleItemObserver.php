<?php

namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    public function created(SaleItem $saleItem): void
    {
        $product = $saleItem->product;
        $product->decrement('stock', $saleItem->quantity);
    }

    public function updated(SaleItem $saleItem): void
    {
        $oldQuantity = $saleItem->getOriginal('quantity');
        $newQuantity = $saleItem->quantity;
        $difference = $newQuantity - $oldQuantity;
        
        $product = $saleItem->product;
        $product->decrement('stock', $difference);
    }

    public function deleted(SaleItem $saleItem): void
    {
        $product = $saleItem->product;
        $product->increment('stock', $saleItem->quantity);
    }
}