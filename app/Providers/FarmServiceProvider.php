<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Stock\Transaction;
use App\Observers\TransactionObserver;

class FarmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       # Transaction::observe(TransactionObserver::class);
    }
}
