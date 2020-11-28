<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class ReservedArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var ReservedArrivalStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(ReservedArrivalStandAllocator::class);
    }

    public function testItAllocatesReservedStandIfActive()
    {
        StandReservation::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'start' => Carbon::now()->subMinute(),
                'end' => Carbon::now()->addHour(),
            ]
        );

        $actual = $this->allocator->allocate(NetworkAircraft::find('BAW123'));
        $expected = StandAssignment::where('callsign', 'BAW123')->first();

        $this->assertEquals($actual->stand_id, 1);
        $this->assertEquals($actual->stand_id, $expected->stand_id);
    }

    public function testItDoesntAllocateReservedStandIfNotActive()
    {
        StandReservation::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'start' => Carbon::now()->addMinute(),
                'end' => Carbon::now()->addHour(),
            ]
        );

        $this->assertNull($this->allocator->allocate(NetworkAircraft::find('BAW123')));
    }

    public function testItDoesntAllocateReservedStandIfNotRightCallsign()
    {
        StandReservation::create(
            [
                'callsign' => 'BAW124',
                'stand_id' => 1,
                'start' => Carbon::now()->subMinute(),
                'end' => Carbon::now()->addHour(),
            ]
        );

        $this->assertNull($this->allocator->allocate(NetworkAircraft::find('BAW123')));
    }

    public function testItDoesntAllocateReservedStandIfNotFree()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW456',
                'stand_id' => 1,
            ]
        );

        StandReservation::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'start' => Carbon::now()->subMinute(),
                'end' => Carbon::now()->addHour(),
            ]
        );

        $this->assertNull($this->allocator->allocate(NetworkAircraft::find('BAW123')));
    }
}
