<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Services\SquawkService;

class MarkAssignmentDeletedOnDisconnect
{
    /**
     * @var SquawkService
     */
    private $squawkService;

    public function __construct(SquawkService $squawkService)
    {
        $this->squawkService = $squawkService;
    }

    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        $this->squawkService->deleteSquawkAssignment($event->getAircraft()->callsign);
        return true;
    }
}
