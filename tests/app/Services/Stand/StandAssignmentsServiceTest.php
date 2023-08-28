<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;

class StandAssignmentsServiceTest extends BaseFunctionalTestCase
{
    private readonly StandAssignmentsService $service;

    private readonly MockInterface $mockHistoryService;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->mockHistoryService = Mockery::mock(RecordsAssignmentHistory::class);
        $this->app->instance(RecordsAssignmentHistory::class, $this->mockHistoryService);
        $this->service = $this->app->make(StandAssignmentsService::class);
    }

    public function testItGetsAnAssignment()
    {
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->assertEquals($assignment->id, $this->service->assignmentForCallsign('BAW123')->id);
    }

    public function testItReturnsNullIfNoAssignment()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->assertNull($this->service->assignmentForCallsign('BAW555'));
    }

    public function testItDeletesAnAssignment()
    {
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->with($assignment)
            ->once();

        $this->service->deleteStandAssignment($assignment);
        $this->assertDatabaseMissing($assignment, ['callsign' => 'BAW123']);
        Event::assertDispatched(fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW123');
    }

    public function testItDeletesAnAssignmentIfExistsForAircraft()
    {
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->with(
                Mockery::on(
                    fn(StandAssignment $assignment): bool => $assignment->callsign === 'BAW123' &&
                    $assignment->stand_id === 1
                )
            )
            ->once();

        $this->service->deleteAssignmentIfExists(NetworkAircraft::find('BAW123'));
        $this->assertDatabaseMissing($assignment, ['callsign' => 'BAW123']);
        Event::assertDispatched(fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW123');
    }

    public function testItDoesntDeleteAnAssignmentIfDoesntExistForAircraft()
    {
        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->never();
        $this->service->deleteAssignmentIfExists(NetworkAircraft::find('BAW123'));
        Event::assertNotDispatched(fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW123');
    }

    public function testCreatingAStandAssignmentThrowsExceptionIfStandDoesntExist()
    {
        $this->expectException(StandNotFoundException::class);
        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->never();
        $this->service->createStandAssignment('BAW123', 999, 'test');
    }

    public function testItCreatesAnAssignment()
    {
        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->with(
                Mockery::on(
                    fn(StandAssignmentContext $context): bool => $context->assignment()->callsign === 'BAW123' &&
                    $context->assignment()->stand_id === 2 &&
                    $context->assignment()->user_id === null &&
                    $context->assignmentType() === 'test' &&
                    $context->removedAssignments()->isEmpty()
                )
            )
            ->once();

        $this->service->createStandAssignment('BAW123', 2, 'test');
        $this->assertDatabaseHas(
            'stand_assignments',
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
                'stand_id' => 1,
            ]
        );

        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->with(
                Mockery::on(
                    fn(StandAssignmentContext $context): bool => $context->assignment()->callsign === 'BAW123' &&
                    $context->assignment()->stand_id === 2 &&
                    $context->assignment()->user_id === null &&
                    $context->assignmentType() === 'test' &&
                    $context->removedAssignments()->isEmpty()
                )
            )
            ->once();

        $this->service->createStandAssignment('BAW123', 2, 'test');
        $this->assertDatabaseHas(
            'stand_assignments',
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

        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->with(
                Mockery::on(
                    fn(StandAssignmentContext $context): bool => $context->assignment()->callsign === 'BAW123' &&
                    $context->assignment()->stand_id === 2 &&
                    $context->assignment()->user_id === null &&
                    $context->assignmentType() === 'test' &&
                    $context->removedAssignments()->isNotEmpty() &&
                    $context->removedAssignments()->count() === 1 &&
                    $context->removedAssignments()->first()->callsign === 'BAW456' &&
                    $context->removedAssignments()->first()->stand_id === 2
                )
            )
            ->once();

        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->with(Mockery::on(fn(StandAssignment $assignment): bool => $assignment->callsign === 'BAW456' && $assignment->stand_id === 2))
            ->once();

        $this->service->createStandAssignment('BAW123', 2, 'test');
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

    public function testCreatingAnAssignmentRemovesExistingForPairedStand()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW456',
                'stand_id' => 2,
            ]
        );
        StandAssignment::create(
            [
                'callsign' => 'BAW789',
                'stand_id' => 3,
            ]
        );
        Stand::findOrFail(1)->pairedStands()->sync([2]);
        Stand::findOrFail(2)->pairedStands()->sync([1]);
        Stand::findOrFail(3)->pairedStands()->sync([2]);
        Stand::findOrFail(2)->pairedStands()->sync([3]);

        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->with(
                Mockery::on(
                    fn(StandAssignmentContext $context): bool => $context->assignment()->callsign === 'BAW123' &&
                    $context->assignment()->stand_id === 2 &&
                    $context->assignment()->user_id === null &&
                    $context->assignmentType() === 'test' &&
                    $context->removedAssignments()->isNotEmpty() &&
                    $context->removedAssignments()->count() === 2 &&
                    $context->removedAssignments()->first()->callsign === 'BAW456' &&
                    $context->removedAssignments()->first()->stand_id === 2 &&
                    $context->removedAssignments()->last()->callsign === 'BAW789' &&
                    $context->removedAssignments()->last()->stand_id === 3
                )
            )
            ->once();

        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->with(Mockery::on(fn(StandAssignment $assignment): bool => $assignment->callsign === 'BAW456' && $assignment->stand_id === 2))
            ->once();
            
        $this->mockHistoryService->shouldReceive('deleteHistoryFor')
            ->with(Mockery::on(fn(StandAssignment $assignment): bool => $assignment->callsign === 'BAW789' && $assignment->stand_id === 3))
            ->once();

        $this->service->createStandAssignment('BAW123', 2, 'test');
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
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'BAW789',
                'stand_id' => 3,
            ]
        );
        Event::assertDispatched(
            fn(StandAssignedEvent $event): bool => $event->getStandAssignment()->callsign === 'BAW123' &&
            $event->getStandAssignment()->stand_id === 2
        );
        Event::assertDispatched(
            fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW456'
        );
        Event::assertDispatched(
            fn(StandUnassignedEvent $event): bool => $event->getCallsign() === 'BAW789'
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

        $this->mockHistoryService->shouldReceive('createHistoryItem')
            ->with(
                Mockery::on(
                    fn(StandAssignmentContext $context): bool => $context->assignment()->callsign === 'BAW123' &&
                    $context->assignment()->stand_id === 2 &&
                    $context->assignment()->user_id === null &&
                    $context->assignmentType() === 'test' &&
                    $context->removedAssignments()->isEmpty()
                )
            )
            ->once();

        $this->service->createStandAssignment('BAW123', 2, 'test');
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
