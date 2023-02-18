<?php

namespace App\Services;

use App\Allocator\Squawk\General\AirfieldPairingSquawkAllocator;
use App\Allocator\Squawk\General\CcamsSquawkAllocator;
use App\Allocator\Squawk\General\OrcamSquawkAllocator;
use App\Allocator\Squawk\Local\UnitDiscreteSquawkAllocator;
use App\BaseFunctionalTestCase;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use TestingUtils\Traits\WithSeedUsers;

class SquawkServiceTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    private SquawkService $squawkService;

    public function setUp(): void
    {
        parent::setUp();
        $this->squawkService = $this->app->make(SquawkService::class);
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testItDeletesSquawks()
    {
        SquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123', 'assignment_type' => 'ORCAM']);
        $this->assertTrue($this->squawkService->deleteSquawkAssignment('BAW123'));
        Event::assertDispatched(SquawkUnassignedEvent::class);

        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'BAW123',
            ]
        );
    }

    public function testReturnsFalseOnNoSquawkDeleted()
    {
        $this->assertFalse($this->squawkService->deleteSquawkAssignment('BAW123'));
        Event::assertNotDispatched(SquawkUnassignedEvent::class);
    }

    public function testItReturnsAssignedSquawk()
    {
        $assignment = SquawkAssignment::find(
            SquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123', 'assignment_type' => 'ORCAM'])->callsign
        );
        $this->assertEquals($assignment, $this->squawkService->getAssignedSquawk('BAW123'));
    }

    public function testItReturnsNullOnNoAssignmentFound()
    {
        $this->assertNull($this->squawkService->getAssignedSquawk('BAW123'));
    }

    public function testItAssignsALocalSquawkAndReturnsIt()
    {
        $assignment = $this->squawkService->assignLocalSquawk('BAW123', 'EGKK_APP', 'I');
        $this->assertEquals('0202', $assignment->getCode());
        $this->assertEquals('UNIT_DISCRETE', $assignment->getType());
        $this->assertEquals('BAW123', $assignment->getCallsign());
        Event::assertDispatched(SquawkAssignmentEvent::class);
    }

    public function testItDoesntAssignLocalSquawkIfAllocatorFails()
    {
        UnitDiscreteSquawkRange::getQuery()->delete();
        $this->assertNull($this->squawkService->assignLocalSquawk('BAW123', 'EGKK_APP', 'I'));
        Event::assertNotDispatched(SquawkAssignmentEvent::class);
    }

    public function testItAssignsAGeneralSquawkAndReturnsIt()
    {
        $assignment = $this->squawkService->assignGeneralSquawk('BAW123', 'KJFK', 'EGLL');
        $this->assertEquals('0101', $assignment->getCode());
        $this->assertEquals('ORCAM', $assignment->getType());
        $this->assertEquals('BAW123', $assignment->getCallsign());
        Event::assertDispatched(SquawkAssignmentEvent::class);
    }

    public function testItDoesntAssignGeneralSquawkIfAllocatorFails()
    {
        OrcamSquawkRange::getQuery()->delete();
        CcamsSquawkRange::getQuery()->delete();
        $this->assertNull($this->squawkService->assignGeneralSquawk('BAW123', 'EGKK', 'EGLL'));
        Event::assertNotDispatched(SquawkAssignmentEvent::class);
    }

    public function testItTriesNextAllocatorIfGeneralAllocationFails()
    {
        CcamsSquawkRange::create(
            [
                'first' => '0303',
                'last' => '0303',
            ]
        );
        OrcamSquawkRange::getQuery()->delete();

        $assignment = $this->squawkService->assignGeneralSquawk('BAW123', 'KJFK', 'EGLL');
        $this->assertEquals('0303', $assignment->getCode());
        $this->assertEquals('CCAMS', $assignment->getType());
        $this->assertEquals('BAW123', $assignment->getCallsign());
        Event::assertDispatched(SquawkAssignmentEvent::class);
    }

    public function testDefaultGeneralAllocatorPreference()
    {
        $expected = [
            AirfieldPairingSquawkAllocator::class,
            OrcamSquawkAllocator::class,
            CcamsSquawkAllocator::class,
        ];

        $this->assertEquals($expected, $this->squawkService->getGeneralAllocatorPreference());
    }

    public function testDefaultLocalAllocatorPreference()
    {
        $expected = [
            UnitDiscreteSquawkAllocator::class,
        ];

        $this->assertEquals($expected, $this->squawkService->getLocalAllocatorPreference());
    }

    public function testReserveActiveSquawksReservesIfNoAssignmentExistsForAircraft()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                'callsign' => 'RYR999',
                'transponder' => '2345',
                'transponder_last_updated_at' => Carbon::now()->subMinutes(3),
                'updated_at' => Carbon::now(),
            ]
        );

        $this->squawkService->reserveActiveSquawks();
        Event::assertDispatched(SquawkAssignmentEvent::class);
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
                'code' => '2345',
                'assignment_type' => 'NON_UKCP',
            ]
        );
    }

    public function testReserveActiveSquawksReservesIfSquawkCodeDifferentToAssignedAndHasntChanged()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                'callsign' => 'RYR999',
                'transponder' => '2345',
                'transponder_last_updated_at' => Carbon::now()->subMinutes(3),
                'updated_at' => Carbon::now(),
            ]
        );
        SquawkAssignment::create(['callsign' => 'RYR999', 'code' => '5678', 'assignment_type' => 'ORCAM']);

        $this->squawkService->reserveActiveSquawks();
        Event::assertDispatched(SquawkAssignmentEvent::class);
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
                'code' => '2345',
                'assignment_type' => 'NON_UKCP',
            ]
        );
    }

    public function testReserveActiveSquawksReservesForTheFirstAircraftWithTheSquawk()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                [
                    'callsign' => 'RYR999',
                    'transponder' => '2345',
                    'transponder_last_updated_at' => Carbon::now()->subMinutes(3),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'WZZ888',
                    'transponder' => '2345',
                    'transponder_last_updated_at' => Carbon::now()->subMinutes(3),
                    'updated_at' => Carbon::now(),
                ],
            ],
        );

        $this->squawkService->reserveActiveSquawks();
        Event::assertDispatched(SquawkAssignmentEvent::class);
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
                'code' => '2345',
                'assignment_type' => 'NON_UKCP',
            ]
        );
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'WZZ888',
            ]
        );
    }

    public function testReserveActiveSquawksDoesntReserveIfSquawkChangedRecently()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                [
                    'callsign' => 'RYR999',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now()->subMinute(),
                    'updated_at' => Carbon::now(),
                ],
            ],
        );

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
        Event::assertNotDispatched(SquawkAssignmentEvent::class);
    }

    public function testReserveActiveSquawksDoesntReserveIfSquawkIsForbiddenCode()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                [
                    'callsign' => 'RYR999',
                    'transponder' => '7700',
                    'transponder_last_updated_at' => Carbon::now()->subMinutes(3)
                ],
            ],
        );

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
        Event::assertNotDispatched(SquawkAssignmentEvent::class);
    }

    public function testReserveActiveSquawksDoesntReserveIfSquawkIsTakenBySomeoneElse()
    {
        DB::table('network_aircraft')->delete();
        DB::table('network_aircraft')->insert(
            [
                [
                    'callsign' => 'RYR999',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now()->subMinutes(3)
                ],
                [
                    'callsign' => 'WZZ888',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now()->subMinutes(3)
                ],
            ],
        );
        SquawkAssignment::create(['callsign' => 'WZZ888', 'code' => '1234', 'assignment_type' => 'ORCAM']);

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
        Event::assertNotDispatched(SquawkAssignmentEvent::class);
    }
}
