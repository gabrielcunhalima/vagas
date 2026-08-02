<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Vite $vite): void
    {
        Schema::defaultStringLength(191);

        $vite->prefetch(concurrency: 3);
    }
}
