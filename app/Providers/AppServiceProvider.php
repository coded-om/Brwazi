<?php

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policies
        \Illuminate\Support\Facades\Gate::policy(Order::class, OrderPolicy::class);
        // Share GPT-5 preview feature flag with all views
        View::share('gpt5PreviewEnabled', (bool) Config::get('features.gpt5_preview', true));
    }
}
