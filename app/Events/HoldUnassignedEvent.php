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

    /**
     * @return string
     */
    public function getCallsign(): string
    {
        return $this->callsign;
    }
}
