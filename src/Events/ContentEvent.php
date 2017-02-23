<?php

namespace Baytek\Laravel\Content\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContentEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $content;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    // public function broadcastAs()
    // {
    //     return 'content.update';
    // }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('content.'.$this->content->id);
    }
}
