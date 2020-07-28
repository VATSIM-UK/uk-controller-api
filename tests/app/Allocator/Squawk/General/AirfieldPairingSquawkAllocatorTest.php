<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\BaseFunctionalTestCase;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class AirfieldPairingSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var AirfieldPairingSquawkAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new AirfieldPairingSquawkAllocator();
    }

    public function testItAllocatesFreeSquawkInRange()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7203');
        $this->createSquawkRange('EGGD', 'EGHH', '7251', '7257');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals(
            '7203',
            $this->allocator->allocate(
                'BMI11A',
                ['origin' => 'EGGD', 'destination' => 'EGFF']
            )->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7203');
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullNoApplicableRange()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull(
            $this->allocator->allocate('BMI11A', ['origin' => 'EGGD', 'destination' => 'EGKK'])
        );
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullMissingOrigin()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull($this->allocator->allocate('BMI11A', ['destination' => 'EGFF']));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullMissingDestination()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGFF']));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullIfAllocationNotFound()
    {
        $this->assertNull($this->allocator->fetch('MMMMM'));
    }

    public function testItReturnsAllocationIfExists()
    {
        $this->createSquawkAssignment('VIR25F', '7201');
        $expected = AirfieldPairingSquawkAssignment::find('VIR25F');

        $this->assertEquals($expected, $this->allocator->fetch('VIR25F'));
    }

    public function testItDeletesAllocations()
    {
        $this->createSquawkAssignment('VIR25F', '7201');

        $this->assertTrue($this->allocator->delete('VIR25F'));
        $this->assertSquawkNotAsssigned('VIR25F');
    }

    public function testItReturnsFalseForNonDeletedAllocations()
    {
        $this->assertFalse($this->allocator->delete('LALALA'));
    }

    private function createSquawkRange(
        string $origin,
        string $destination,
        string $first,
        string $last
    ) {
        AirfieldPairingSquawkRange::create(
            [
                'origin' => $origin,
                'destination' => $destination,
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
                'callsign' => $callsign
            ]
        );
        AirfieldPairingSquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $code,
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'airfield_pairing_squawk_assignments',
            [
                'callsign' => $callsign
            ]
        );
    }

    private function assertSquawkAssigned(string $callsign, string $code)
    {
        $this->assertDatabaseHas(
            'airfield_pairing_squawk_assignments',
            [
                'callsign' => $callsign,
                'code' => $code,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItAssignsToCallsignIfFree()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '0001', '0007');
        $this->assertEquals('0002', $this->allocator->assignToCallsign('0002', 'RYR111')->getCode());
        $this->assertSquawkAssigned('RYR111', '0002');
    }

    public function testItDoesntAssignIfNotInRange()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0010'));
        $this->assertSquawkNotAsssigned('RYR111');
    }

    public function testItDoesntAssignIfAlreadyAssigned()
    {
        $this->createSquawkAssignment('RYR234', '0001');
        $this->createSquawkRange('EGGD', 'EGFF', '0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0001'));
        $this->assertSquawkNotAsssigned('RYR111');
        $this->assertSquawkAssigned('RYR234', '0001');
    }
}
