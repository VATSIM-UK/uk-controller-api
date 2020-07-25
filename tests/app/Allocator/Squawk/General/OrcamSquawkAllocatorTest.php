<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\BaseFunctionalTestCase;
use App\Models\Squawk\Orcam\OrcamSquawkAssignment;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class OrcamSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var OrcamSquawkAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new OrcamSquawkAllocator();
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        $this->createSquawkRange('E', '7201', '7210');
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
        $this->createSquawkRange('E', '7201', '7210');

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

    public function testItReturnsNullIfAllocationNotFound()
    {
        $this->assertNull($this->allocator->fetch('MMMMM'));
    }

    public function testItReturnsAllocationIfExists()
    {
        $this->createSquawkAssignment('VIR25F', '0001');
        $expected = OrcamSquawkAssignment::find('VIR25F');

        $this->assertEquals($expected, $this->allocator->fetch('VIR25F'));
    }

    public function testItDeletesAllocations()
    {
        $this->createSquawkAssignment('VIR25F', '0001');

        $this->assertTrue($this->allocator->delete('VIR25F'));
        $this->assertSquawkNotAsssigned('VIR25F');
    }

    public function testItReturnsFalseForNonDeletedAllocations()
    {
        $this->assertFalse($this->allocator->delete('LALALA'));
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
        OrcamSquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $code,
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'orcam_squawk_assignments',
            [
                'callsign' => $callsign
            ]
        );
    }

    private function assertSquawkAssigned(string $callsign, string $code)
    {
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => $callsign,
                'code' => $code,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItAssignsToCallsignIfFree()
    {
        $this->createSquawkRange('L', '0001', '0007');
        $this->assertEquals('0002', $this->allocator->assignToCallsign('0002', 'RYR111')->getCode());
        $this->assertSquawkAssigned('RYR111', '0002');
    }

    public function testItDoesntAssignIfNotInRange()
    {
        $this->createSquawkRange('L', '0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0010'));
        $this->assertSquawkNotAsssigned('RYR111');
    }

    public function testItDoesntAssignIfAlreadyAssigned()
    {
        $this->createSquawkAssignment('RYR234', '0001');
        $this->createSquawkRange('L', '0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0001'));
        $this->assertSquawkNotAsssigned('RYR111');
        $this->assertSquawkAssigned('RYR234', '0001');
    }
}
