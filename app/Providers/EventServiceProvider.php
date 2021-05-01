<?php

namespace App\Providers;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\MetarsUpdatedEvent;
use App\Events\NetworkDataUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Metar\UpdateMinimumStackLevels;
use App\Listeners\Network\NetworkDataUpdated;
use App\Listeners\Squawk\MarkAssignmentHistoryDeletedOnUnassignment;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Stand\DeleteAssignmentHistoryOnUnassignment;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use App\Models\Stand\Stand;
use App\Observers\StandObserver;
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
        NetworkDataUpdatedEvent::class => [
            NetworkDataUpdated::class,
        ],
        StandAssignedEvent::class => [
            RecordStandAssignmentHistory::class,
        ],
        StandUnassignedEvent::class => [
            DeleteAssignmentHistoryOnUnassignment::class,
        ],
        MetarsUpdatedEvent::class => [
            UpdateMinimumStackLevels::class,
        ],
    ];

    public function boot()
    {
        Stand::observe(StandObserver::class);
    }
}
