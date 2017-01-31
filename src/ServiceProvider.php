<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Policies\ContentPolicy;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class ServiceProvider extends AuthServiceProvider
{

    protected $policies = [
        Content::class => ContentPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
        $this->registerPolicies();
        $this->loadRoutesFrom(__DIR__.'/Routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../resources/Migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/Views', 'Pretzel');

        //dd($this->policies);
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