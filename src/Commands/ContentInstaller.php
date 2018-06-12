<?php
namespace Baytek\Laravel\Content\Commands;

use Baytek\Laravel\Content\ContentServiceProvider;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Seeders\ContentSeeder;
use Spatie\Permission\Models\Permission;

use Artisan;
use DB;

class ContentInstaller extends Installer
{
    /**
     * Name of model we are installing
     *
     * @var string
     */
    public $name = 'Content';

    /**
     * List of model that we should protect
     *
     * @var array
     */
    protected $protected = ['Content'];

    /**
     * Content Service provider reference
     *
     * @var Baytek\Laravel\Content\ContentServiceProvider
     */
    protected $provider = ContentServiceProvider::class;

    /**
     * Content Model reference
     *
     * @var Baytek\Laravel\Content\Models\Content
     */
    protected $model = Content::class;


    /**
     * Content Seeder reference
     *
     * @var Baytek\Laravel\Content\Seeders\ContentSeeder
     */
    protected $seeder = ContentSeeder::class;

    /**
     * Should we seed fake data?
     *
     * @var bool
     */
    protected $fakeSeeder = false;

    /**
     * Location of the migrations
     *
     * @var string
     */
    protected $migrationPath = __DIR__.'/../database/Migrations';

    /**
     * Sample method of beforeInstallation
     *
     * @return void
     */
    public function beforeInstallation()
    {
    }

    /**
     * Sample method of afterInstallation
     *
     * @return void
     */
    public function afterInstallation()
    {
    }

    /**
     * Method to determine if we should publish
     *
     * @return bool
     */
    public function shouldPublish()
    {
        return true;
    }

    /**
     * Method to determine if we should protect
     *
     * @return bool
     */
    public function shouldProtect()
    {
        foreach ($this->protected as $model) {
            foreach(['view', 'create', 'update', 'delete'] as $permission) {

                // If the permission exists in any form do not reseed.
                if(Permission::where('name', title_case($permission.' '.$model))->exists()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to determine if we should migrate
     *
     * @return bool
     */
    public function shouldMigrate()
    {
        $pluginTables = [
            env('DB_PREFIX', '').'contents',
            env('DB_PREFIX', '').'content_meta',
            env('DB_PREFIX', '').'content_histories',
            env('DB_PREFIX', '').'content_relations',
        ];

        return collect(array_map('reset', DB::select('SHOW TABLES')))
            ->intersect($pluginTables)
            ->isEmpty();
    }

    /**
     * Method to determine if we should seed
     *
     * @return bool
     */
    public function shouldSeed()
    {
        $relevantRecords = [
            'root',
            'content-type',
            'relation-type',
            'parent-id',
        ];

        return (new $this->model)->whereIn('contents.key', $relevantRecords)->count() === 0;
    }
}
