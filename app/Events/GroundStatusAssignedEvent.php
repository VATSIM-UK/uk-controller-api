<?php

namespace App\Events;

use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroundStatusAssignedEvent implements ShouldBroadcast
{
    const CHANNEL = 'ground-statuses';

    /**
     * @var string
     */
    private $callsign;

    /**
     * @var int
     */
    private $groundStatusId;

    /**
     * GroundStatusAssignedEvent constructor.
     * @param string $callsign
     * @param int $groundStatusId
     */
    public function __construct(string $callsign, int $groundStatusId)
    {
        $this->callsign = $callsign;
        $this->groundStatusId = $groundStatusId;
    }

    /**
     * @return array|int[]
     */
    public function broadcastWith(): array
    {
        return [
            $this->callsign => $this->groundStatusId
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

    /**
     * @return int
     */
    public function getGroundStatusId(): int
    {
        return $this->groundStatusId;
    }
}
