<?php

namespace Baytek\Laravel\Content\Listeners;

use Baytek\Laravel\Content\Events\ContentEvent;

use DB;
use Cache;

class ContentNursurySubscriber
{
    /**
     * Handle the creation or update of content.
     */
    public function nurse($event)
    {
        // Cache::forget('nursery');
        $cache = json_decode(Cache::get('nursery', file_exists(database_path() . '/nursery.json') ?
                file_get_contents(database_path() . '/nursery.json') : '[]'
        ), true);

        $cache[$event->content->key] = $event->content->load(['meta', 'relations', 'relations.relation', 'relations.relationType']);

        $json = json_encode($cache, JSON_PRETTY_PRINT + JSON_NUMERIC_CHECK);
        Cache::forever('nursery', $json);

        file_put_contents(database_path() . '/nursery.json', $json);
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
            static::class.'@nurse'
        );
    }
}
