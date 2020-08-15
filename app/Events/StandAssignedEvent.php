<?php

namespace App\Events;

use App\Models\Stand\StandAssignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StandAssignedEvent implements ShouldBroadcast
{
    const CHANNEL = 'stand-assignments';

    /**
     * @var StandAssignment
     */
    private $standAssignment;

    public function __construct(StandAssignment $standAssignment)
    {
        $this->standAssignment = $standAssignment;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->standAssignment->callsign,
            'stand_id' => $this->standAssignment->stand_id,
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
