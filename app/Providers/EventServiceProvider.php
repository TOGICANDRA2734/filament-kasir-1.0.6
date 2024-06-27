<?php

namespace App\Providers;

use App\Models\OrderProduct;
use App\Observers\OrderProductObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $observers = [
        OrderProduct::class => OrderProductObserver::class,
    ];

    public function boot()
    {
        parent::boot();
    }
}
