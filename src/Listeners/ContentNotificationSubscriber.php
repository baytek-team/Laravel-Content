<?php

namespace Baytek\Laravel\Content\Listeners;

use Baytek\Laravel\Content\Events\ContentEvent;

use DB;
use Cache;

class ContentNotificationSubscriber
{
    /**
     * Handle user login events.
     */
    public function create($event)
    {
        // dump('Content Event Fires');
    }

    /**
     * Cache the content id value pairs for quicker lookups
     * @param  Baytek\Laravel\Content\Events\ContentEvent  $event Content event class
     * @return void
     */
    public function cache($event)
    {
        $dates = DB::table('contents')->select('id', 'updated_at')->get();

        $result = collect([]);

        $dates->each(function(&$item) use (&$result) {
            $result->put($item->id, $item->updated_at);
        });

        $json = $result->toJson();

        Cache::forever('content.cache.hash', md5($json));
        Cache::forever('content.cache.json', $json);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            ContentEvent::class,
            static::class.'@create'
        );

        $events->listen(
            ContentEvent::class,
            static::class.'@cache'
        );
    }
}
