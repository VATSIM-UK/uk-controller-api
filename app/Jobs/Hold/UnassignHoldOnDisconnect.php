<?php

namespace App\Jobs\Hold;

use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnassignHoldOnDisconnect implements ShouldQueue
{
    use Dispatchable, SerializesModels, Queueable;

    private NetworkAircraft $disconnectingAircraft;

    public function __construct(NetworkAircraft $disconnectingAircraft)
    {
        $this->disconnectingAircraft = $disconnectingAircraft;
    }

    public function handle(): void
    {
        $callsign = $this->disconnectingAircraft->callsign;
        if (AssignedHold::destroy($callsign)) {
            event(new HoldUnassignedEvent($callsign));
        }
    }
}
