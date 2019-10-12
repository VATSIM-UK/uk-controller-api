<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MinStacksUpdatedEvent implements ShouldBroadcast
{
    const CHANNEL = 'minstack-updates';

    /**
     * @var array
     */
    public $airfield;

    /**
     * @var array
     */
    public $tma;

    /**
     * AirfieldMinStacksUpdatedEvent constructor.
     * @param array $airfield
     * @param array $tma
     */
    public function __construct(array $airfield, array $tma)
    {
        $this->airfield = $airfield;
        $this->tma = $tma;
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
