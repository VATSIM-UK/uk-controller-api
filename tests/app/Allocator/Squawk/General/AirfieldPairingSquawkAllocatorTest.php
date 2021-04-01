<?php

namespace App\Allocator\Squawk\General;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;

class AirfieldPairingSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var AirfieldPairingSquawkAllocator
     */
    private AirfieldPairingSquawkAllocator $allocator;

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

    public function testItAllocatesSingleCharacterDestinationRange()
    {
        $this->createSquawkRange('EGJJ', 'E', '7201', '7201');

        $this->assertEquals(
            '7201',
            $this->allocator->allocate('BMI11A', ['origin' => 'EGJJ', 'destination' => 'EGGD'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7201');
    }

    public function testItPrefersDoubleCharacterDestinationMatchOverSingle()
    {
        $this->createSquawkRange('EGJJ', 'E', '7201', '7201');
        $this->createSquawkRange('EGJJ', 'EG', '7202', '7202');

        $this->assertEquals(
            '7202',
            $this->allocator->allocate('BMI11A', ['origin' => 'EGJJ', 'destination' => 'EGGD'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7202');
    }

    public function testItPrefersTripleCharacterDestinationMatchOverDouble()
    {
        $this->createSquawkRange('EGJJ', 'EG', '7201', '7201');
        $this->createSquawkRange('EGJJ', 'EG', '7202', '7202');
        $this->createSquawkRange('EGJJ', 'EGG', '7203', '7203');

        $this->assertEquals(
            '7203',
            $this->allocator->allocate('BMI11A', ['origin' => 'EGJJ', 'destination' => 'EGGD'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7203');
    }

    public function testItPrefersQuadrupleCharacterDestinationMatchOverTriple()
    {
        $this->createSquawkRange('EGJJ', 'EG', '7201', '7201');
        $this->createSquawkRange('EGJJ', 'EG', '7202', '7202');
        $this->createSquawkRange('EGJJ', 'EGG', '7203', '7203');
        $this->createSquawkRange('EGJJ', 'EGGD', '7204', '7204');

        $this->assertEquals(
            '7204',
            $this->allocator->allocate('BMI11A', ['origin' => 'EGJJ', 'destination' => 'EGGD'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7204');
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAssigned('BMI11A');
    }

    public function testItReturnsNullNoApplicableRange()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull(
            $this->allocator->allocate('BMI11A', ['origin' => 'EGGD', 'destination' => 'EGKK'])
        );
        $this->assertSquawkNotAssigned('BMI11A');
    }

    public function testItReturnsNullMissingOrigin()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull($this->allocator->allocate('BMI11A', ['destination' => 'EGFF']));
        $this->assertSquawkNotAssigned('BMI11A');
    }

    public function testItReturnsNullMissingDestination()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');

        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGFF']));
        $this->assertSquawkNotAssigned('BMI11A');
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
        SquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $code,
                'assignment_type' => 'AIRFIELD_PAIR'
            ]
        );
    }

    private function assertSquawkNotAssigned(string $callsign)
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
                'assignment_type' => 'AIRFIELD_PAIR'
            ]
        );
    }
}
