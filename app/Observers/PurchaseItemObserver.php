<?php

namespace App\Observers;

use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    public function created(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $product->increment('stock', $purchaseItem->quantity);
    }

    public function updated(PurchaseItem $purchaseItem): void
    {
        $oldQuantity = $purchaseItem->getOriginal('quantity');
        $newQuantity = $purchaseItem->quantity;
        $difference = $newQuantity - $oldQuantity;
        
        $product = $purchaseItem->product;
        $product->increment('stock', $difference);
    }

    public function deleted(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $product->decrement('stock', $purchaseItem->quantity);
    }
}