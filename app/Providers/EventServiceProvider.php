<?php

namespace App\Providers;

use App\Events\Database\DatabaseTablesUpdated;
use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\MetarsUpdatedEvent;
use App\Events\NetworkDataUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Listeners\Database\MigrationsFinished;
use App\Listeners\Dependency\UpdateDependencies;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Metar\MetarsUpdated;
use App\Listeners\Network\NetworkDataUpdated;
use App\Listeners\Squawk\MarkAssignmentHistoryDeletedOnUnassignment;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Stand\DeleteAssignmentHistoryOnUnassignment;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use App\Models\Hold\Hold;
use App\Models\Stand\Stand;
use App\Observers\HoldObserver;
use App\Observers\StandObserver;
use Illuminate\Database\Events\MigrationsEnded;
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
            MetarsUpdated::class,
        ],
        MigrationsEnded::class => [
            MigrationsFinished::class,
        ],
        DatabaseTablesUpdated::class => [
            UpdateDependencies::class,
        ],
    ];
}
