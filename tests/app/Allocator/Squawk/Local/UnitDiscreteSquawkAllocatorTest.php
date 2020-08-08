<?php

namespace App\Allocator\Squawk\Local;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\BaseFunctionalTestCase;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkAssignment;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeRule;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnitDiscreteSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var UnitDiscreteSquawkAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new UnitDiscreteSquawkAllocator();
        $this->createSquawkRange('EGGD', '7201', '7203');
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        $this->createSquawkAssignment('VIR25F', 'EGGD', '7201');
        $this->createSquawkAssignment('BAW92A', 'EGGD', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => '7203',
            ]
        );
    }

    public function testItReducesUnitToBaseForm()
    {
        $this->createSquawkAssignment('VIR25F', 'EGGD', '7201');
        $this->createSquawkAssignment('BAW92A', 'EGGD', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD_APP'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
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
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => $code,
            ]
        );
    }

    public function testItIgnoresOverlappingIfDifferentUnits()
    {
        $this->createSquawkRange('EGFF', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', 'EGFF', '7201');
        $this->createSquawkAssignment('BAW92A', 'EGFF', '7202');

        $code = $this->allocator->allocate('BMI11A', ['unit' => 'EGGD'])->getCode();
        $this->assertTrue(in_array($code, ['7201', '7202', '7203']));
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
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
    }

    public function testItReturnsNullOnAllSquawksAllocated()
    {
        $this->createSquawkRange('EGFF', '7201', '7202');
        $this->createSquawkAssignment('VIR25F', 'EGFF', '7201');
        $this->createSquawkAssignment('BAW92A', 'EGFF', '7202');

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

    public function testItReturnsNullIfAllocationNotFound()
    {
        $this->assertNull($this->allocator->fetch('MMMMM'));
    }

    public function testItReturnsAllocationIfExists()
    {
        $this->createSquawkAssignment('VIR25F', 'EGGD', '0001');
        $expected = UnitDiscreteSquawkAssignment::find('VIR25F');

        $this->assertEquals($expected, $this->allocator->fetch('VIR25F'));
    }

    public function testItDeletesAllocations()
    {
        $this->createSquawkAssignment('VIR25F', 'EGGD', '0001');

        $this->assertTrue($this->allocator->delete('VIR25F'));
        $this->assertSquawkNotAsssigned('VIR25F');
    }

    public function testItReturnsFalseForNonDeletedAllocations()
    {
        $this->assertFalse($this->allocator->delete('LALALA'));
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
        string $unit,
        string $code
    ) {
        NetworkAircraft::create(
            [
                'callsign' => $callsign
            ]
        );
        UnitDiscreteSquawkAssignment::create(
            [
                'callsign' => $callsign,
                'unit' => $unit,
                'code' => $code,
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => $callsign
            ]
        );
    }
}
