<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Vente;
use App\Models\StockLot;

use App\Observers\VenteObserver;
use App\Observers\StockLotObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Vente::observe(VenteObserver::class);
        StockLot::observe(StockLotObserver::class);
    }
}