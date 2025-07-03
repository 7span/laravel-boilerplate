<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\LogCleanupCommand;

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
        if ($this->app->runningInConsole()) {
            $this->commands([
                LogCleanupCommand::class,
            ]);
        }
    }
}
