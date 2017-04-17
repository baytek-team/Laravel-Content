<?php
namespace Baytek\Laravel\Content\Commands;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;

use Faker\Generator;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factory;

class MakeContentSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:content-seeder {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new content seeder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $className = studly_case($this->argument('name'));

        $outputFile = './database/seeds/'.$className.'.php';

        if(file_exists($outputFile)) {
            if(!$this->confirm('File exists! Do you wish to continue?')) {
                return false;
            }
        }

        $seeder = file_get_contents(__DIR__.'/../Seeders/SampleSeeder.php');
        $seeder = str_replace('{{namespace}}', '', $seeder);
        $seeder = str_replace('{{seederName}}', $className, $seeder);

        file_put_contents($outputFile, $seeder);
    }
}