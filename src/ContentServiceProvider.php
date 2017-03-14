<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Content\Policies\ContentPolicy;
use Baytek\Laravel\Content\Middleware\LocaleMiddleware;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factory;
use Artisan;
use Event;
use DB;

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
    public function boot(\Illuminate\Routing\Router $router)
    {
        // AliasLoader::getInstance()->alias('Form', 'Collective\Html\FormFacade');
        $this->registerPolicies();
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

        $this->publishes([
            __DIR__.'/../resources/Config/content.php' => config_path('content.php'),
            __DIR__.'/../resources/Config/language.php' => config_path('language.php'),
        ], 'config');

        $this->bootArtisanCommands();

        $router->group([
            'namespace' => '\Baytek\Laravel\Content\Controllers',
            'prefix' => 'admin',
            'middleware' => ['web', 'auth', LocaleMiddleware::class]
        ], function () use ($router) {
            $router->resource('content', 'ContentController');
            $router->resource('translation', 'ContentController');
        });

    }

    public function bootArtisanCommands()
    {
        (new ContentInstaller)->installCommand();
        // $this->bootArtisanInstallCommand();
        $this->bootArtisanSeedCommand();
    }

    public function bootArtisanSeedCommand()
    {
        Artisan::command('content:seed', function () {
            $this->info("Seeding Content");

            if(app()->environment() === 'production') {
                $this->error("You are in a production environment, aborting.");
                exit();
            }

            $faker = new Generator;
            $factory = app(Factory::class);

            $factory->define(Content::class, function (Generator $faker) {
                static $password;

                $title = $faker->sentence(rand(2, 10));

                return [
                    'key' => str_slug($title),
                    'title' => $title,
                    'content' => $faker->paragraph(rand(2, 10)),
                ];
            });

            factory(Content::class, 1000)->create()->each(function ($content, $index) {
                // Save item as webpage
                (new ContentRelation([
                    'content_id'  => $content->id,
                    'relation_id' => 4,
                    'relation_type_id' => 2,
                ]))->save();

                // Pick a random piece of content as parent id
                (new ContentRelation([
                    'content_id'  => $content->id,
                    'relation_id' => Content::ofContentType('webpage')->inRandomOrder()->limit(1)->first()->id,
                    'relation_type_id' => 3,
                ]))->save();
            });

            // $factory->define(Baytek\Laravel\Content\Models\ContentMeta::class, function (Faker\Generator $faker) {
            //     static $password;

            //     $title = $faker->sentence();

            //     return [
            //         'key' => str_slug($title),
            //         'title' => $title,
            //         'content' => $faker->paragraphs(rand(2, 10)),
            //     ];
            // });

            // Here we need to check to see if the base content was already seeded.

            $this->info("Seeding Base Content");
            // (new \Baytek\Laravel\Content\Seeds\RandomSeeder)->run();


        })->describe('Seed tables with random data');
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Baytek\Laravel\Settings\SettingsServiceProvider::class);
        $this->app->register(\Baytek\LaravelStatusBit\StatusBitServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Users\ServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Content\ContentEventServiceProvider::class);
        $this->app->register(\Baytek\Laravel\Menu\MenuServiceProvider::class);
    	// $this->app->register('Collective\Html\HtmlServiceProvider');
    }
}
