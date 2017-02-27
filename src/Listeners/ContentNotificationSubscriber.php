<?php

namespace Baytek\Laravel\Content\Listeners;

use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Listeners\ContentNotificationSubscriber;

class ContentNotificationSubscriber
{
    /**
     * Handle user login events.
     */
    public function create($event) {
        // dump('Content Event Fires');
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
            ContentNotificationSubscriber::class.'@create'
        );
    }
}
