<?php

namespace kpasokhi\savano;

use Illuminate\Support\ServiceProvider;

class SavanoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // bind the Savano Facade
        $this->app->bind('Savano', function () {
            return new Savano();
        });
    }
}