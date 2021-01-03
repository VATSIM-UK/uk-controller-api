<?php

namespace App\Events;

use App\Models\Departure\DepartureRestriction;
use App\Models\Hold\AssignedHold;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DepartureRestrictionUpdatedEvent implements ShouldBroadcast
{
    const CHANNEL = 'departure-intervals';

    /**
     * @var DepartureRestriction
     */
    private DepartureRestriction $interval;

    public function __construct(DepartureRestriction $interval)
    {
        $this->interval = $interval;
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
