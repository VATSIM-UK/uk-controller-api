<?php

namespace App\Jobs\Squawk;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\SquawkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarkAssignmentDeletedOnDisconnect implements ShouldQueue
{
    use Dispatchable, SerializesModels, Queueable;

    private NetworkAircraft $disconnectingAircraft;

    public function __construct(NetworkAircraft $disconnectingAircraft)
    {
        $this->disconnectingAircraft = $disconnectingAircraft;
    }

    public function handle(SquawkService $squawkService): void
    {
        $squawkService->deleteSquawkAssignment($this->disconnectingAircraft->callsign);
    }
}
