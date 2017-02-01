<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Policies\ContentPolicy;
use Baytek\LaravelStatusBit\StatusBitServiceProvider;
use Baytek\Laravel\User\ServiceProvider as UserServiceProvider;
use Prettus\Repository\Providers\RepositoryServiceProvider;

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
        $this->loadViewsFrom(__DIR__.'/../resources/Views', 'Content');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(StatusBitServiceProvider::class);
        $this->app->register(UserServiceProvider::class);
    	// $this->app->register('Collective\Html\HtmlServiceProvider');
    }
}