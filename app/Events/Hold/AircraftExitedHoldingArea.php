<?php

namespace App\Events\Hold;

use App\Events\HighPriorityBroadcastEvent;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Broadcasting\PrivateChannel;

class AircraftExitedHoldingArea extends HighPriorityBroadcastEvent
{
    private NetworkAircraft $aircraft;
    private Navaid $navaid;

    public function __construct(NetworkAircraft $aircraft, Navaid $navaid)
    {
        $this->aircraft = $aircraft;
        $this->navaid = $navaid;
    }

    public function broadcastWith(): array
    {
        return [
            'callsign' => $this->aircraft->callsign,
            'navaid_id' => $this->navaid->id,
        ];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('holds')];
    }

    public function broadcastAs(): string
    {
        return 'hold.area-exited';
    }
}
