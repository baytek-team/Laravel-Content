<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Content\Policies\ContentPolicy;
use Baytek\Laravel\Content\Middleware\LocaleMiddleware;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;

use Artisan;
use DB;
use Event;
use Validator;

class ContentServiceProvider extends AuthServiceProvider
{
    /**
     * List of artisan commands to be added to command kernel
     * @var array
     */
    protected $commands = [
        Commands\MakeContentSeeder::class,
        Commands\RandomContentSeeder::class,
        Commands\CacheContent::class,
        Commands\ContentInstaller::class,
    ];

    /**
     * List of policies to be registered to the AuthServiceProvider
     * @var array
     */
    protected $policies = [
        Content::class => ContentPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        // AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
        $this->registerPolicies();
        $this->loadViewsFrom(__DIR__.'/../views', 'Content');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/content'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__.'/../database/Migrations');
        $this->publishes([
            __DIR__.'/../database/Migrations/' => database_path('migrations')
        ], 'migrations');

        // $this->publishes([
        //     __DIR__.'/../database/Seeds/' => database_path('seeds')
        // ], 'seeds');

        $this->publishes([
            __DIR__.'/../config/content.php' => config_path('content.php'),
            __DIR__.'/../config/language.php' => config_path('language.php'),
        ], 'config');

        $router->group([
            'namespace' => '\Baytek\Laravel\Content\Controllers',
            'prefix' => 'admin',
            'middleware' => ['web', 'auth', LocaleMiddleware::class]
        ], function () use ($router) {
            $router->resource('content', 'ContentManagementController');
            $router->put('translation/{content}/translate', 'ContentController@translate')->name('translation.translate');
            $router->get('translation/create', 'ContentController@contentCreate')->name('translation.create');
            $router->get('translation/{content}/edit', 'ContentController@contentEdit')->name('translation.edit');

            // $router->resource('translation', 'ContentController');
        });

        Validator::extend('unique_key', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $id = isset($data['id']) ? $data['id']: null;
            $parent_id = $data[$parameters[1]];
            $children = Content::childrenOf($parent_id, 'id')->get()
                ->filter(function ($item, $key) use ($id) {
                    return $item->id != $id;
                });
            return !$children->pluck('key')->contains(str_slug($value));
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register commands
        $this->commands($this->commands);

        if(config('app.debug') && class_exists(\Clockwork\Support\Laravel\ClockworkServiceProvider::class)) {
            $this->app->register(\Clockwork\Support\Laravel\ClockworkServiceProvider::class);
        }

        $this->app->register(\Baytek\Laravel\Settings\SettingsServiceProvider::class);
        $this->app->register(\Baytek\LaravelStatusBit\StatusBitServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Users\ServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Content\ContentEventServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Menu\MenuServiceProvider::class);
    	$this->app->register(\Collective\Html\HtmlServiceProvider::class);
    }
}
