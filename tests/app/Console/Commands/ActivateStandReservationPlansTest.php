<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Stand\StandReservationsImport as Importer;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandReservationPlan;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandAssignmentsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Mockery;

class ActivateStandReservationPlansTest extends BaseFunctionalTestCase
{
    private $mockImporter;
    private $mockAssignmentsService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(Importer::class);
        $this->app->instance(Importer::class, $this->mockImporter);

        $this->mockAssignmentsService = Mockery::mock(StandAssignmentsService::class);
        $this->app->instance(StandAssignmentsService::class, $this->mockAssignmentsService);

        Cache::flush();
    }

    public function testItImportsApprovedPlansWhenEventStartHasBegun(): void
    {
        $plan = StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->subHour()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '531',
                        'slot_reservations' => [
                            [
                                'callsign' => 'BAW1234',
                                'start' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
                                'end' => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(2),
            'approved_at' => now()->subDay(),
            'status' => 'approved',
            'imported_reservations' => null,
        ]);

        $this->mockImporter->shouldReceive('importReservations')
            ->once()
            ->with(Mockery::on(fn (Collection $rows): bool => $rows->count() === 1 && $rows->first()->get('stand') === '531'))
            ->andReturn(1);

        $this->mockAssignmentsService->shouldReceive('assignmentForCallsign')->never();
        $this->mockAssignmentsService->shouldReceive('createStandAssignment')->never();
        $this->mockAssignmentsService->shouldReceive('deleteStandAssignment')->never();

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'imported_reservations' => 1,
        ]);
    }

    public function testItSkipsPlansWithFutureEventStart(): void
    {
        $plan = StandReservationPlan::create([
            'name' => 'Future Event',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '531',
                        'slot_reservations' => [
                            [
                                'callsign' => 'BAW9999',
                                'start' => now()->addDay()->format('Y-m-d H:i:s'),
                                'end' => now()->addDay()->addMinutes(30)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(2),
            'approved_at' => now()->subDay(),
            'status' => 'approved',
            'imported_reservations' => null,
        ]);

        $this->mockImporter->shouldReceive('importReservations')->never();
        $this->mockAssignmentsService->shouldReceive('assignmentForCallsign')->never();
        $this->mockAssignmentsService->shouldReceive('createStandAssignment')->never();
        $this->mockAssignmentsService->shouldReceive('deleteStandAssignment')->never();

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'imported_reservations' => null,
        ]);
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

        $this->mockImporter->shouldReceive('importReservations')->never();

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

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertEquals([
            'BAW1234' => 1,
        ], Cache::get('stand_reservations:managed_assignments'));
    }
}
