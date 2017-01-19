<?php

namespace Baytek\LaravelContent;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    	// AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	// $this->app->register('Collective\Html\HtmlServiceProvider');

    }
}