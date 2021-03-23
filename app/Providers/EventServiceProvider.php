<?php

namespace App\Providers;

use App\Models\Stand\Stand;
use App\Observers\StandObserver;
use App\Events\HoldAssignedEvent;
use App\Events\StandVacatedEvent;
use App\Events\StandAssignedEvent;
use App\Events\StandOccupiedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\StandUnassignedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Listeners\Stand\OccupyStands;
use App\Listeners\Network\RecordFirEntry;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Squawk\ReserveInFirProximity;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Listeners\Hold\UnassignHoldOnDisconnect;
use App\Listeners\Squawk\ReclaimIfLeftFirProximity;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use App\Listeners\Stand\UnassignVacatedDepartureStand;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Stand\TriggerUnassignmentOnDisconnect;
use App\Listeners\Stand\AssignOccupiedStandsForDeparture;
use App\Listeners\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Listeners\Squawk\MarkAssignmentHistoryDeletedOnUnassignment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\Stand\DeleteAssignmentHistoryOnUnassignment as MarkStandAssignmentDeletedOnUnassignment;

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

    public function boot()
    {
        Stand::observe(StandObserver::class);
    }
}
