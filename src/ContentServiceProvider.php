<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Content\Policies\ContentPolicy;
use Baytek\Laravel\Content\Middleware\LocaleMiddleware;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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
    public function boot(Router $router)
    {
        // AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
        $this->registerPolicies();
        $this->loadViewsFrom(__DIR__.'/../views', 'content');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/content'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

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
        });


        if(!$router->has('admin.index')) {
            $router->group([
                'as' => 'admin.',
                'namespace' => '\Baytek\Laravel\Content\Controllers',
                'middleware' => ['web', 'auth']
            ], function () use ($router) {

                $router->get('admin', function(){
                    return view('content::admin');
                })->name('index');
            });
        }


        // 'title' => 'required|unique_key:contents,parent_id',
        Validator::extend('unique_key', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $route = \Route::getCurrentRoute();
            $id = null;

            // Check if the route params are set, if so use it.
            if(count($route->parameters())) {
                $id = collect($route->parameters())->last();
                if (is_object($id)) {
                    $id = $id->id;
                }
            }

            if(array_key_exists($parameters[1], $data)) {
                $parent_id = $data[$parameters[1]];

                $children = Content::childrenOf($parent_id, 'id')->get()
                    ->filter(function ($item, $key) use ($id) {
                        return $item->id != $id;
                    });
                //dd(!$children->pluck('key')->contains(str_slug($value)));
                return !$children->pluck('key')->contains(str_slug($value));
            }
            else {
                return true;
            }

        });

        Validator::extend('unique_in_type', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $route = Route::getCurrentRoute();
            $id = null;

            // Check if the route params are set, if so use it.
            if(count($route->parameters())) {
                $id = collect($route->parameters())->last();

                if(is_object($id) && $id instanceof Model) {
                    // Sorry, I need just the id.
                    $id = $id->id;
                }
            }

            $children = Content::ofType($parameters[0])->get()
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

        $this->app->register(\Laracasts\Flash\FlashServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Settings\SettingsServiceProvider::class);
        $this->app->register(\Baytek\LaravelStatusBit\StatusBitServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Users\ServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Content\ContentEventServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Menu\MenuServiceProvider::class);
    	$this->app->register(\Collective\Html\HtmlServiceProvider::class);
    }
}
