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
        // dd((string)$event->content->load('meta'));
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
