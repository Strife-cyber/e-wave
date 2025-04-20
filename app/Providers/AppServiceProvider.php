<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\GenerateModelResources;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            GenerateModelResources::class
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
