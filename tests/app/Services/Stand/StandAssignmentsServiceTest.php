<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use Illuminate\Support\Facades\Event;

class StandAssignmentsServiceTest extends BaseFunctionalTestCase
{
    private readonly StandAssignmentsService $service;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->service = $this->app->make(StandAssignmentsService::class);
    }

    public function testItDeletesAnAssignment()
    {
        $history = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 1,
        ]);
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->service->deleteStandAssignment($assignment);
        $this->assertDatabaseMissing($assignment, ['callsign' => 'BAW123']);
        $this->assertSoftDeleted($history->refresh());
        Event::assertDispatched(fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW123');
    }

    public function testItCreatesAnAssignment()
    {
        $this->service->createStandAssignment('BAW123', 2);
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );

        Event::assertDispatched(
            fn(StandAssignedEvent $event): bool => $event->getStandAssignment()->callsign === 'BAW123' &&
                $event->getStandAssignment()->stand_id === 2
        );
    }

    public function testItUpdatesAnAircraftsAssignment()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1
            ]
        );
        $this->service->createStandAssignment('BAW123', 2);
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );

        Event::assertDispatched(
            fn(StandAssignedEvent $event): bool => $event->getStandAssignment()->callsign === 'BAW123' &&
                $event->getStandAssignment()->stand_id === 2
        );
    }

    public function testCreatingAnAssignmentRemovesExistingForSameStand()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW456',
                'stand_id' => 2,
            ]
        );
        $this->service->createStandAssignment('BAW123', 2);
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'BAW456',
                'stand_id' => 2,
            ]
        );
        Event::assertDispatched(
            fn(StandAssignedEvent $event): bool => $event->getStandAssignment()->callsign === 'BAW123' &&
                $event->getStandAssignment()->stand_id === 2
        );
        Event::assertDispatched(
            fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW456'
        );
    }

    public function testCreatingAnAssignmentDoesntRemoveExistingForDifferentStand()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW456',
                'stand_id' => 1,
            ]
        );
        $this->service->createStandAssignment('BAW123', 2);
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW456',
                'stand_id' => 1,
            ]
        );
        Event::assertDispatched(
            fn(StandAssignedEvent $event): bool => $event->getStandAssignment()->callsign === 'BAW123' &&
                $event->getStandAssignment()->stand_id === 2
        );
    }
}
