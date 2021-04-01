<?php

namespace App\Allocator\Squawk\Local;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeRule;
use App\Models\Vatsim\NetworkAircraft;

class UnitDiscreteSquawkAllocatorTest extends BaseFunctionalTestCase
{
    private UnitDiscreteSquawkAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new UnitDiscreteSquawkAllocator();
        $this->createSquawkRange('EGGD', '7201', '7203');
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD'])->getCode());
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'assignment_type' => 'UNIT_DISCRETE',
                'code' => '7203',
            ]
        );
    }

    public function testItReducesUnitToBaseForm()
    {
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD_APP'])->getCode());
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'assignment_type' => 'UNIT_DISCRETE',
                'code' => '7203',
            ]
        );
    }

    public function testItIncludesGuestRanges()
    {
        UnitDiscreteSquawkRangeGuest::create(
            [
                'primary_unit' => 'EGGD',
                'guest_unit' => 'EGFF',
            ]
        );

        $code = $this->allocator->allocate('BMI11A', ['unit' => 'EGFF'])->getCode();
        $this->assertTrue(in_array($code, ['7201', '7202', '7203']));
        $this->assertDatabaseHas(
            'squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'assignment_type' => 'UNIT_DISCRETE',
                'code' => $code,
            ]
        );
    }

    public function testItFiltersRangesWhereRulesDoNotPass()
    {
        $range = $this->createSquawkRange('EGFF', '7201', '7210');
        UnitDiscreteSquawkRangeRule::create(
            [
                'unit_discrete_squawk_range_id' => $range->id,
                'rule' => [
                    'rule' => 'TWR',
                    'type' => 'UNIT_TYPE',
                ],
            ]
        );
        $this->assertNull($this->allocator->allocate('BMI11A', ['unit' => 'EGFF_APP']));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('EGFF', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertSquawkNotAsssigned('BMI11A');
    }

    public function testItReturnsNullNoUnitProvided()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', []));
    }

    public function testItReturnsNullOnNoApplicableRange()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', ['unit' => 'EGHH']));
    }

    private function createSquawkRange(
        string $unit,
        string $first,
        string $last
    ): UnitDiscreteSquawkRange {
        return UnitDiscreteSquawkRange::create(
            [
                'unit' => $unit,
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
                'assignment_type' => 'UNIT_DISCRETE',
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
}
