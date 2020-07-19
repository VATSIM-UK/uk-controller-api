<?php

namespace App\Providers;

use App\Events\GroundStatusAssignedEvent;
use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Listeners\GroundStatus\RecordGroundStatusHistory;
use App\Listeners\GroundStatus\UnassignOnDisconnect;
use App\Listeners\Network\RecordFirEntry;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Hold\UnassignHoldOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SquawkAssignmentEvent::class => [
            RecordSquawkAssignmentHistory::class,
        ],
        HoldAssignedEvent::class => [
            RecordHoldAssignment::class,
        ],
        HoldUnassignedEvent::class => [
            RecordHoldUnassignment::class,
        ],
        NetworkAircraftDisconnectedEvent::class => [
            UnassignHoldOnDisconnect::class,
            UnassignOnDisconnect::class,
            MarkAssignmentDeletedOnDisconnect::class,
        ],
        NetworkAircraftUpdatedEvent::class => [
            RecordFirEntry::class,
        ],
        GroundStatusAssignedEvent::class => [
            RecordGroundStatusHistory::class
        ],
    ];
}
