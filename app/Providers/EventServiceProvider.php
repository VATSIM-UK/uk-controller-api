<?php

namespace App\Providers;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Events\StandAssignedEvent;
use App\Events\StandOccupiedEvent;
use App\Events\StandUnassignedEvent;
use App\Events\StandVacatedEvent;
use App\Listeners\Network\RecordFirEntry;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Hold\UnassignHoldOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentHistoryDeletedOnUnassignment;
use App\Listeners\Squawk\ReclaimIfLeftFirProximity;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Squawk\ReserveInFirProximity;
use App\Listeners\Stand\AssignOccupiedStandsForDeparture;
use App\Listeners\Stand\DeleteAssignmentHistoryOnUnassignment as MarkStandAssignmentDeletedOnUnassignment;
use App\Listeners\Stand\OccupyStands;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use App\Listeners\Stand\TriggerUnassignmentOnDisconnect;
use App\Listeners\Stand\UnassignVacatedDepartureStand;
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
            MarkAssignmentHistoryDeletedOnUnassignment::class,
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
            TriggerUnassignmentOnDisconnect::class,
        ],
        NetworkAircraftUpdatedEvent::class => [
            // RecordFirEntry::class, This is quite intensive on CPU and isn't used at the moment
            ReserveInFirProximity::class,
            ReclaimIfLeftFirProximity::class,
            OccupyStands::class,
        ],
        StandAssignedEvent::class => [
            RecordStandAssignmentHistory::class,
        ],
        StandUnassignedEvent::class => [
            MarkStandAssignmentDeletedOnUnassignment::class,
        ],
        StandOccupiedEvent::class => [
            AssignOccupiedStandsForDeparture::class,
        ],
        StandVacatedEvent::class => [
            UnassignVacatedDepartureStand::class,
        ]
    ];
}
