<?php

namespace App\Jobs\Stand;

use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TriggerUnassignmentOnDisconnect implements ShouldQueue
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

        if (StandAssignment::destroy($callsign)) {
            event(new StandUnassignedEvent($callsign));
        }
    }
}
