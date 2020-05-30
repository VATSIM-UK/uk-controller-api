<?php

namespace App\Events;

use App\Models\Hold\AssignedHold;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HoldUnassignedEvent implements ShouldBroadcast
{
    const CHANNEL = 'hold-assignments';

    /**
     * @var AssignedHold
     */
    private $hold;

    public function __construct(AssignedHold $hold)
    {
        $this->hold = $hold;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->hold->callsign,
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
