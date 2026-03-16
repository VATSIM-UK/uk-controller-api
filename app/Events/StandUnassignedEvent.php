<?php

namespace App\Events;

use App\Models\Stand\StandAssignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class StandUnassignedEvent extends HighPriorityBroadcastEvent
{
    const CHANNEL = 'stand-assignments';

    /**
     * @var string
     */
    private $callsign;

    private ?string $assignmentSource;

    public function __construct(
        string $callsign,
        ?string $assignmentSource = null,
    ){
        $this->callsign = $callsign;
        $this->assignmentSource = $assignmentSource;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->callsign,
            'stand_id' => null,
            'assignment_source' => $this->assignmentSource ?? StandAssignment::SOURCE_SYSTEM_AUTO,
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
