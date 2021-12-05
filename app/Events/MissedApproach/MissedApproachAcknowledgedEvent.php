<?php

namespace App\Events\MissedApproach;

use App\Events\HighPriorityBroadcastEvent;
use App\Models\MissedApproach\MissedApproachNotification;
use Illuminate\Broadcasting\PrivateChannel;

class MissedApproachAcknowledgedEvent extends HighPriorityBroadcastEvent
{
    private MissedApproachNotification $missedApproach;
    private string $acknowledgedBy;

    public function __construct(MissedApproachNotification $missedApproach, string $acknowledgedBy)
    {
        $this->missedApproach = $missedApproach;
        $this->acknowledgedBy = $acknowledgedBy;
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->missedApproach->id,
            'acknowledged_by' => $this->acknowledgedBy,
            'remarks' => $this->missedApproach->remarks,
        ];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('missed-approaches')];
    }

    public function broadcastAs(): string
    {
        return 'missed-approach.acknowledged';
    }
}
