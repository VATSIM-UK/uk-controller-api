<?php

namespace App\Events;

use App\Services\Stand\StandAssignmentPayload;
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

    private string $assignmentStatus;

    private bool $assignedByReservationAllocator;

    private bool $assignedByPilotRequest;

    public function __construct(
        string $callsign,
        ?string $assignmentSource = null,
        string $assignmentStatus = StandAssignmentPayload::STATUS_ASSIGNED,
        bool $assignedByReservationAllocator = false,
        bool $assignedByPilotRequest = false,
    )
    {
        $this->callsign = $callsign;
        $this->assignmentSource = $assignmentSource;
        $this->assignmentStatus = $assignmentStatus;
        $this->assignedByReservationAllocator = $assignedByReservationAllocator;
        $this->assignedByPilotRequest = $assignedByPilotRequest;
    }

    public function broadcastWith()
    {
        return [
            'callsign' => $this->callsign,
            'stand_id' => null,
            'assigned_by_reservation_allocator' => $this->assignedByReservationAllocator,
            'assigned_by_pilot_request' => $this->assignedByPilotRequest,
            'assignment_source' => $this->assignmentSource ?? StandAssignmentPayload::SOURCE_SYSTEM_AUTO,
            'assignment_status' => $this->assignmentStatus,
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
