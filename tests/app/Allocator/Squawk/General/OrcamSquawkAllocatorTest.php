<?php

namespace App\Allocator\Squawk\General;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;

class OrcamSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var OrcamSquawkAllocator
     */
    private OrcamSquawkAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new OrcamSquawkAllocator();
    }

    public function testItAllocatesFreeSquawkInRange()
    {
        $this->createSquawkRange('E', '7201', '7203');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals(
            '7203',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7203');
    }

    public function testItAllocatesSingleCharacterRange()
    {
        $this->createSquawkRange('E', '7201', '7201');

        $this->assertEquals(
            '7201',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7201');
    }

    public function testItPrefersDoubleCharacterMatchOverSingle()
    {
        $this->createSquawkRange('E', '7201', '7201');
        $this->createSquawkRange('ED', '7202', '7202');

        $this->assertEquals(
            '7202',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7202');
    }

    public function testItPrefersTripleCharacterMatchOverDouble()
    {
        $this->createSquawkRange('E', '7201', '7201');
        $this->createSquawkRange('ED', '7202', '7202');
        $this->createSquawkRange('EDD', '7203', '7203');

        $this->assertEquals(
            '7203',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7203');
    }

    public function testItPrefersFullMatch()
    {
        $this->createSquawkRange('E', '7201', '7201');
        $this->createSquawkRange('ED', '7202', '7202');
        $this->createSquawkRange('EDD', '7203', '7203');
        $this->createSquawkRange('EDDF', '7204', '7204');

        $this->assertEquals(
            '7204',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertSquawkAssigned('BMI11A', '7204');
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('E', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullOnNoApplicableRange()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGGD']));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullOnMissingOrigin()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    private function createSquawkRange(
        string $origin,
        string $first,
        string $last
    ) {
        OrcamSquawkRange::create(
            [
                'origin' => $origin,
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
                'assignment_type' => 'ORCAM',
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'squawk_assignments',
            [
                'callsign' => $callsign
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
                'assignment_type' => 'ORCAM',
            ]
        );
    }
}
