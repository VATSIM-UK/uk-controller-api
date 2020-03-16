<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RegionalPressuresUpdatedEvent implements ShouldBroadcast
{
    const CHANNEL = 'rps-updates';

    /**
     * @var array
     */
    public $pressures;

    /**
     * AirfieldMinStacksUpdatedEvent constructor.
     * @param array $pressures
     */
    public function __construct(array $pressures)
    {
        $this->pressures = $pressures;
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
