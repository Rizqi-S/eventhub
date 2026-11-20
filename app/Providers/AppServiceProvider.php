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
        // Memaksa Laravel menggunakan /tmp untuk storage saat di Vercel
        if (env('APP_STORAGE')) {
            $this->app->useStoragePath(env('APP_STORAGE'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}