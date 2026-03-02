<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandAssignmentsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Mockery;

class SyncStandReservationAssignmentsTest extends BaseFunctionalTestCase
{
    private $mockAssignmentsService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockAssignmentsService = Mockery::mock(StandAssignmentsService::class);
        $this->app->instance(StandAssignmentsService::class, $this->mockAssignmentsService);

        Cache::flush();
    }

    public function testItAssignsStandToMatchingActiveReservationCallsignAndLiftsExpiredSlotAssignments(): void
    {
        StandReservation::create([
            'stand_id' => 1,
            'callsign' => 'BAW1234',
            'cid' => 1234567,
            'origin' => 'EGCC',
            'destination' => 'EGLL',
            'start' => now()->subMinutes(5),
            'end' => now()->addMinutes(20),
        ]);

        NetworkAircraft::create([
            'callsign' => 'BAW1234',
            'cid' => 1234567,
            'planned_depairport' => 'EGCC',
            'planned_destairport' => 'EGLL',
        ]);

        NetworkAircraft::create([
            'callsign' => 'OLD123',
        ]);

        StandAssignment::create([
            'callsign' => 'OLD123',
            'stand_id' => 2,
        ]);

        Cache::forever('stand_reservations:managed_assignments', [
            'OLD123' => 2,
        ]);

        $this->mockAssignmentsService->shouldReceive('assignmentForCallsign')
            ->once()
            ->with('BAW1234')
            ->andReturn(null);

        $this->mockAssignmentsService->shouldReceive('createStandAssignment')
            ->once()
            ->with('BAW1234', 1, 'Reservation');

        $this->mockAssignmentsService->shouldReceive('deleteStandAssignment')
            ->once()
            ->with(Mockery::on(fn (StandAssignment $assignment): bool => $assignment->callsign === 'OLD123' && $assignment->stand_id === 2));

        $this->assertEquals(0, Artisan::call('stand-reservations:sync-assignments'));

        $this->assertEquals([
            'BAW1234' => 1,
        ], Cache::get('stand_reservations:managed_assignments'));
    }
    public function testItPrioritisesCidMatchingOverCallsignWhenCidIsPresent(): void
    {
        StandReservation::create([
            'stand_id' => 3,
            'callsign' => 'BAW1234',
            'cid' => 9999999,
            'origin' => 'EGCC',
            'destination' => 'EGLL',
            'start' => now()->subMinutes(5),
            'end' => now()->addMinutes(20),
        ]);

        NetworkAircraft::create([
            'callsign' => 'BAW1234',
            'cid' => 1111111,
            'planned_depairport' => 'EGCC',
            'planned_destairport' => 'EGLL',
        ]);

        NetworkAircraft::create([
            'callsign' => 'DLH4321',
            'cid' => 9999999,
            'planned_depairport' => 'EGCC',
            'planned_destairport' => 'EGLL',
        ]);

        $this->mockAssignmentsService->shouldReceive('assignmentForCallsign')
            ->once()
            ->with('DLH4321')
            ->andReturn(null);

        $this->mockAssignmentsService->shouldReceive('createStandAssignment')
            ->once()
            ->with('DLH4321', 3, 'Reservation');

        $this->mockAssignmentsService->shouldReceive('deleteStandAssignment')->never();

        $this->assertEquals(0, Artisan::call('stand-reservations:sync-assignments'));

        $this->assertEquals([
            'DLH4321' => 3,
        ], Cache::get('stand_reservations:managed_assignments'));
    }

}
