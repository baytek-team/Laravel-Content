<?php
namespace Baytek\Laravel\Content\Commands;

use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content;

use Faker\Generator;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factory;

class CacheContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the content ID\'s and keys';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new ContentEvent(Content::find(1)));

        // We are done, display message indicating so.
        $this->info("Completed cache of content");
    }
}