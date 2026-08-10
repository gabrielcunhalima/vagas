<?php

namespace App\Providers;

use App\Models\Vagas\Candidatura;
use App\Policies\CandidaturaPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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

        Gate::policy(Candidatura::class, CandidaturaPolicy::class);

        $vite->prefetch(concurrency: 3);
    }
}
