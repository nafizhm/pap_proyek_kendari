<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer(['admin.layout_admin', 'admin.login'], function ($view) {
            $view->with('appBranding', app(\App\Services\ApplicationBranding::class)->identity());
        });
    }
}
