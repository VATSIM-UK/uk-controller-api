<?php


namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AirfieldMinStacksUpdatedEvent implements ShouldBroadcast
{
    const CHANNEL = 'minstack-updates';

    /**
     * @var array
     */
    private $airfield;
    /**
     * @var array
     */
    private $tma;

    /**
     * AirfieldMinStacksUpdatedEvent constructor.
     * @param array $minStacks
     */
    public function __construct(array $airfield, array $tma)
    {
        $this->airfield = $airfield;
        $this->tma = $tma;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn() : array
    {
        return [self::CHANNEL];
    }
}
