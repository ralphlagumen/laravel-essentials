<?php

namespace Lagumen\Essential;

use Illuminate\Support\ServiceProvider;
use Lagumen\Essential\Console\LaravelEssentialMakeValidation;

class LaravelEssentialServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel_essential.php' => config_path('laravel_essential.php'),
        ], 'laravel-essential-config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel_essential.php',
            'laravel-essential-config'
        );

        // Register commands..
        if ($this->app->runningInConsole()) {
            $this->commands(LaravelEssentialMakeValidation::class);
        }
    }
}
