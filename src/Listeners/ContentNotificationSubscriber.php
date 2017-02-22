<?php

namespace Baytek\Laravel\Content\Listeners;

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
            \Baytek\Laravel\Content\Events\ContentEvent::class,
            \Baytek\Laravel\Content\Listeners\ContentNotificationSubscriber::class.'@create'
        );
    }

}