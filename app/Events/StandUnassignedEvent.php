<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StandUnassignedEvent implements ShouldBroadcast
{
    const CHANNEL = 'stand-assignments';

    /**
     * @var string
     */
    private $callsign;

    public function __construct(string $callsign)
    {
        $this->callsign = $callsign;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->callsign,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel[]
     */
    public function broadcastOn() : array
    {
        return [new PrivateChannel(self::CHANNEL)];
    }
}
