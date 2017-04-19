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
        $dates = DB::table('contents')->select('id', 'key', 'updated_at')->get();
        $relations = DB::table('content_relations')->get();
        $timestamps = collect([]);
        $keys = collect([]);

        $dates->each(function(&$item) use (&$timestamps, &$keys) {
            $timestamps->put($item->id, $item->updated_at);
            $keys->put($item->id, $item->key);
        });

        $json = $timestamps->toJson();

        Cache::forever('content.cache.hash', md5($json));
        Cache::forever('content.cache.json', $json);
        Cache::forever('content.cache.keys', $keys);
        Cache::forever('content.cache.relations', $relations);
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
