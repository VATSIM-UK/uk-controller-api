<?php

namespace App\Events\MissedApproach;

use App\Events\HighPriorityBroadcastEvent;
use App\Models\MissedApproach\MissedApproachNotification;
use Illuminate\Broadcasting\PrivateChannel;

class MissedApproachEvent extends HighPriorityBroadcastEvent
{
    private MissedApproachNotification $missedApproach;

    public function __construct(MissedApproachNotification $missedApproach)
    {
        $this->missedApproach = $missedApproach;
    }

    public function broadcastWith(): array
    {
        return [
            'callsign' => $this->missedApproach->callsign,
        ];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('missed-approaches')];
    }

    public function broadcastAs(): string
    {
        return 'missed-approach.created';
    }
}
