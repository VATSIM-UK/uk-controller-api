<?php

namespace App\Events;

use App\Models\Hold\AssignedHold;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class HoldAssignedEvent extends HighPriorityBroadcastEvent
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
            'navaid' => $this->hold->navaid->identifier,
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

    /**
     * @return AssignedHold
     */
    public function getHold(): AssignedHold
    {
        return $this->hold;
    }
}
