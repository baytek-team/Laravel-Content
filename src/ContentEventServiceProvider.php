<?php

namespace Baytek\Laravel\Content;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class ContentEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        Listeners\ContentNotificationSubscriber::class,
        Listeners\ContentNursurySubscriber::class,
    ];
}