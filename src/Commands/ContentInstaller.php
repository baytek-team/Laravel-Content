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
    public $name = 'Content';
    protected $protected = ['Content'];
    protected $provider = ContentServiceProvider::class;
    protected $model = Content::class;
    protected $seeder = ContentSeeder::class;
    protected $fakeSeeder = false;
    protected $migrationPath = __DIR__.'/../database/Migrations';

    public function beforeInstallation()
    {
    }

    public function afterInstallation()
    {
    }

    public function shouldPublish()
    {
        return true;
    }

    public function shouldProtect()
    {
        foreach ($protected as $model) {
            foreach(['view', 'create', 'update', 'delete'] as $permission) {

                // If the permission exists in any form do not reseed.
                if(Permission::where('name', title_case($permission.' '.$model)->exists()) {
                    return false;
                }
            }
        }

        return true;
    }

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

    public function shouldSeed()
    {
        $relevantRecords = [
            'root',
            'content-type',
            'relation-type',
            'parent-id',
        ];

        return (new $this->model)->whereIn('key', $relevantRecords)->count() === 0;
    }
}