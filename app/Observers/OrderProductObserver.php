<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class OrderProductObserver
{
    public function created(OrderProduct $orderProduct)
    {
        Log::info('OrderProduct created observer called');
        $product = Product::find($orderProduct->product_id);
        $product->decrement('stock', $orderProduct->quantity);
    }

    public function updated(OrderProduct $orderProduct)
    {
        Log::info('OrderProduct updated observer called');
        $product = Product::find($orderProduct->product_id);
        $originalQuantity = $orderProduct->getOriginal('quantity');
        $newQuantity = $orderProduct->quantity;

        if ($originalQuantity != $newQuantity) {
            $product->increment('stock', $originalQuantity);
            $product->decrement('stock', $newQuantity);
        }
    }

    public function deleted(OrderProduct $orderProduct)
    {
        Log::info('OrderProduct deleted observer called');
        $product = Product::find($orderProduct->product_id);
        $product->increment('stock', $orderProduct->quantity);
    }
}
