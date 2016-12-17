<?php

namespace Baytek\LaravelContent;

use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    	AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->register('Collective\Html\HtmlServiceProvider');

    }
}