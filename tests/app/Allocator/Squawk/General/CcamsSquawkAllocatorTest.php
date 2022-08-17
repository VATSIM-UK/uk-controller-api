<?php

namespace App\Allocator\Squawk\General;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;

class CcamsSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var CcamsSquawkAllocator
     */
    private CcamsSquawkAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new CcamsSquawkAllocator();
        CcamsSquawkRange::query()->delete();
    }

    public function testItDoesntAllocateBannedSquawks()
    {
        $this->createSquawkRange('1234', '1234');
        $this->createSquawkRange('0200', '0200');
        $this->createSquawkRange('1200', '1200');
        $this->createSquawkRange('1201', '1201');

        $this->assertEquals('1201', $this->allocator->allocate('BMI11A', [])->getCode());
        $this->assertSquawkAssigned('BMI11A', '1201');
    }

    public function testItAllocatesFreeSquawkInRange()
    {
        $this->createSquawkRange('7201', '7203');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', [])->getCode());
        $this->assertSquawkAssigned('BMI11A', '7203');
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('7201', '7202');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullOnNoApplicableRange()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullIfAllocationFails()
    {
        $this->assertNull($this->allocator->allocate('BAW123', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    private function createSquawkRange(
        string $first,
        string $last
    ) {
        CcamsSquawkRange::create(
            [
                'first' => $first,
                'last' => $last,
            ]
        );
    }

    private function createSquawkAssignment(
        string $callsign,
        string $code
    ) {
        NetworkAircraft::create(
            [
                'callsign' => $callsign,
            ]
        );
        SquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $code,
                'assignment_type' => 'CCAMS',
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => $callsign,
            ]
        );
    }

    private function assertSquawkAssigned(string $callsign, string $code)
    {
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => $callsign,
                'code' => $code,
                'assignment_type' => 'CCAMS',
            ]
        );
    }
}
