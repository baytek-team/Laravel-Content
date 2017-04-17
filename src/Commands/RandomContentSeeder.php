<?php
namespace Baytek\Laravel\Content\Commands;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentRelation;

use Faker\Generator;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factory;

class RandomContentSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:random-seed {records=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the content tables with random data (defaults to 1000 records)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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

        factory(Content::class, (int)$this->argument('records'))->create()->each(function ($content, $index) {
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

        // We are done, display message indicating so.
        $this->info("Random seed complete!");
    }
}