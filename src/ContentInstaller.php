<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\ContentServiceProvider;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Seeders\ContentSeeder;

use Artisan;
use DB;

class ContentInstaller extends Installer
{
    public $name = 'Content';
    protected $provider = ContentServiceProvider::class;
    protected $model = Content::class;
    protected $seeder = ContentSeeder::class;
    protected $migrationPath = __DIR__.'/../resources/Database/Migrations';

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

    public function shouldMigrate()
    {
        $pluginTables = [
            env('DB_PREFIX', '').'contents',
            env('DB_PREFIX', '').'content_metas',
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
