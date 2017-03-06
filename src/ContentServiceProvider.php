<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Content\Policies\ContentPolicy;

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

        $this->bootArtisanCommands();

    }

    public function bootArtisanCommands()
    {
        $this->bootArtisanInstallCommand();
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

    public function bootArtisanInstallCommand()
    {
        Artisan::command('install:content', function () {

            $pluginTables = [
                env('DB_PREFIX', '').'contents',
                env('DB_PREFIX', '').'content_metas',
                env('DB_PREFIX', '').'content_histories',
                env('DB_PREFIX', '').'content_relations',
            ];

            $relaventRecords = [
                'root',
                'content-type',
                'relation-type',
                'parent-id',
            ];

            $this->info('Installing base content type package.');
            $this->comment('Doing checks to see if migrations, seeding and publishing need to happen.');

            if(app()->environment() === 'production') {
                $this->error('You are in a production environment, aborting.');
                exit();
            }

            $databaseTables = collect(array_map('reset', DB::select('SHOW TABLES')));

            $this->line('');
            $this->line('Checking if migrations are required: ');

            if($databaseTables->intersect($pluginTables)->isEmpty()) {
                $this->info('Yes! Running Migrations.');
                Artisan::call('migrate');
                // Artisan::call('migrate', ['--path' => __DIR__.'/../resources/Database/Migrations']);
            }
            else {
                $this->comment('No! Skipping.');
            }

            $this->line('');
            $this->line('Checking if base data seeding is required: ');
            $recordCount = Content::whereIn('key', $relaventRecords)->count();

            if($recordCount === 0) {
                $this->info('Yes! Running Seeder.');

                (new \Baytek\Laravel\Content\Seeds\ContentSeeder)->run();
            }
            else if($recordCount === count($relaventRecords)) {
                $this->comment('No! Skipping.');
            }
            else {
                $this->comment('Warning! Some of the records exist already, there may be an issue with your installation. Skipping.');
            }

            if($this->confirm('Would your like to publish and/or overwrite publishable assets?')) {
                $this->info('Publishing Assets.');
                Artisan::call('vendor:publish', ['--tag' => 'views', '--provider' => Baytek\Laravel\Content\ContentServiceProvider::class]);
            }

            $this->line('');
            $this->info('Installation Complete.');

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
