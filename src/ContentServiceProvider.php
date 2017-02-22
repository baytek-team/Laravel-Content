<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Policies\ContentPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;

use Artisan;
use Event;

class ContentServiceProvider extends AuthServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/Views', 'Content');

        $this->publishes([
            __DIR__.'/../resources/Views' => resource_path('views/vendor/Content'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__.'/../resources/Database/Migrations');
        $this->publishes([
            __DIR__.'/../resources/Database/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../resources/Database/Seeds/' => database_path('seeds')
        ], 'seeds');

        Artisan::command('content:install', function () {
            $this->info("Installing Content");

            if(app()->environment() === 'production') {
                $this->error("You are in a production environment, aborting.");
                exit();
            }

            $this->info("Running Migrations");
            Artisan::call('migrate', ['--path' => __DIR__.'/../resources/Database/Migrations']);

            // Here we need to check to see if the base content was already seeded.

            $this->info("Seeding Base Content");
            (new \Baytek\Laravel\Content\Seeds\ContentSeeder)->run();

            $this->info("Publishing Assets");
            Artisan::call('vendor:publish', ['--tag' => 'views', '--provider' => Baytek\Laravel\Content\ContentServiceProvider::class]);

        })->describe('Install the base system and seed the content tables');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Baytek\Laravel\Settings\SettingsServiceProvider::class);
        $this->app->register(\Prettus\Repository\Providers\RepositoryServiceProvider::class);
        $this->app->register(\Baytek\LaravelStatusBit\StatusBitServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Users\ServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Content\ContentEventServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Menu\MenuServiceProvider::class);
    	// $this->app->register('Collective\Html\HtmlServiceProvider');
    }
}