<?php

namespace App\Providers;

use App\Events\Database\DatabaseTablesUpdated;
use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Events\MetarsUpdatedEvent;
use App\Events\NetworkControllersUpdatedEvent;
use App\Events\NetworkDataUpdatedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Filament\Helpers\SelectOptions;
use App\Listeners\Database\MigrationsFinished;
use App\Listeners\Dependency\UpdateDependencies;
use App\Listeners\Hold\RecordHoldAssignment;
use App\Listeners\Hold\RecordHoldUnassignment;
use App\Listeners\Metar\MetarsUpdated;
use App\Listeners\Network\NetworkControllersUpdated;
use App\Listeners\Network\NetworkDataUpdated;
use App\Listeners\Squawk\MarkAssignmentHistoryDeletedOnUnassignment;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;
use App\Listeners\Stand\DeleteAssignmentHistoryOnUnassignment;
use App\Listeners\Stand\RecordStandAssignmentHistory;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategoryScheme;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Hold\Hold;
use App\Models\IntentionCode\FirExitPoint;
use App\Models\Runway\Runway;
use App\Models\Stand\Stand;
use App\Observers\HoldObserver;
use App\Observers\SelectOptionsObserver;
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
        MetarsUpdatedEvent::class => [
            MetarsUpdated::class,
        ],
        MigrationsEnded::class => [
            MigrationsFinished::class,
        ],
        DatabaseTablesUpdated::class => [
            UpdateDependencies::class,
        ],
        NetworkControllersUpdatedEvent::class => [
            NetworkControllersUpdated::class,
        ],
    ];

    protected $observers = [
        Aircraft::class => SelectOptionsObserver::class,
        Airfield::class => SelectOptionsObserver::class,
        Airline::class => SelectOptionsObserver::class,
        ControllerPosition::class => SelectOptionsObserver::class,
        FirExitPoint::class => SelectOptionsObserver::class,
        Handoff::class => SelectOptionsObserver::class,
        Runway::class => SelectOptionsObserver::class,
        Stand::class => StandObserver::class,
        WakeCategoryScheme::class => SelectOptionsObserver::class,
    ];

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
