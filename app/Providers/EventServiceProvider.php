<?php

namespace App\Providers;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Events\StandAssignedEvent;
use App\Listeners\Network\RecordFirEntry;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Hold\UnassignHoldOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentDeletedOnUnassignment;
use App\Listeners\Squawk\ReclaimIfLeftFirProximity;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Squawk\ReserveInFirProximity;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use Illuminate\Foundation\Application;
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
        SquawkUnassignedEvent::class => [
            MarkAssignmentDeletedOnUnassignment::class,
        ],
        HoldAssignedEvent::class => [
            RecordHoldAssignment::class,
        ],
        HoldUnassignedEvent::class => [
            RecordHoldUnassignment::class,
        ],
        NetworkAircraftDisconnectedEvent::class => [
            UnassignHoldOnDisconnect::class,
            MarkAssignmentDeletedOnDisconnect::class,
        ],
        NetworkAircraftUpdatedEvent::class => [
            RecordFirEntry::class,
            ReserveInFirProximity::class,
            ReclaimIfLeftFirProximity::class,
        ],
        StandAssignedEvent::class => [
            RecordStandAssignmentHistory::class,
        ],
    ];
}
