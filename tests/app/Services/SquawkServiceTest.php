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
    }

    public function testItDeletesSquawks()
    {
        $this->expectsEvents(SquawkUnassignedEvent::class);

        SquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123', 'assignment_type' => 'ORCAM']);
        $this->assertTrue($this->squawkService->deleteSquawkAssignment('BAW123'));

        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'BAW123',
            ]
        );
    }

    public function testReturnsFalseOnNoSquawkDeleted()
    {
        $this->doesntExpectEvents(SquawkUnassignedEvent::class);
        $this->assertFalse($this->squawkService->deleteSquawkAssignment('BAW123'));
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
        $this->expectsEvents(SquawkAssignmentEvent::class);
        $assignment = $this->squawkService->assignLocalSquawk('BAW123', 'EGKK_APP', 'I');
        $this->assertEquals('0202', $assignment->getCode());
        $this->assertEquals('UNIT_DISCRETE', $assignment->getType());
        $this->assertEquals('BAW123', $assignment->getCallsign());
    }

    public function testItDoesntAssignLocalSquawkIfAllocatorFails()
    {
        $this->doesntExpectEvents(SquawkAssignmentEvent::class);
        UnitDiscreteSquawkRange::getQuery()->delete();
        $this->assertNull($this->squawkService->assignLocalSquawk('BAW123', 'EGKK_APP', 'I'));
    }

    public function testItAssignsAGeneralSquawkAndReturnsIt()
    {
        $this->expectsEvents(SquawkAssignmentEvent::class);
        $assignment = $this->squawkService->assignGeneralSquawk('BAW123', 'KJFK', 'EGLL');
        $this->assertEquals('0101', $assignment->getCode());
        $this->assertEquals('ORCAM', $assignment->getType());
        $this->assertEquals('BAW123', $assignment->getCallsign());
    }

    public function testItDoesntAssignGeneralSquawkIfAllocatorFails()
    {
        $this->doesntExpectEvents(SquawkAssignmentEvent::class);
        OrcamSquawkRange::getQuery()->delete();
        CcamsSquawkRange::getQuery()->delete();
        $this->assertNull($this->squawkService->assignGeneralSquawk('BAW123', 'EGKK', 'EGLL'));
    }

    public function testItTriesNextAllocatorIfGeneralAllocationFails()
    {
        $this->expectsEvents(SquawkAssignmentEvent::class);
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
        $this->expectsEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
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
        $this->expectsEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
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
        $this->expectsEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
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
        $this->doesntExpectEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
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
        $this->doesntExpectEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
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
        $this->doesntExpectEvents(SquawkAssignmentEvent::class);

        $this->squawkService->reserveActiveSquawks();
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => 'RYR999',
            ]
        );
    }
}
