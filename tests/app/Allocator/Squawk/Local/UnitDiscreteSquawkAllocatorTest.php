<?php

namespace App\Allocator\Squawk\Local;

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

        UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGGD',
                'first' => '7201',
                'last' => '7210',
            ]
        );
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        NetworkAircraft::insert(
            [
                [
                    'callsign' => 'VIR25F',
                ],
                [
                    'callsign' => 'BAW92A',
                ],
            ]
        );

        UnitDiscreteSquawkAssignment::insert(
            [
                [
                    'callsign' => 'VIR25F',
                    'code' => '7201',
                    'unit' => 'EGGD',
                ],
                [
                    'callsign' => 'BAW92A',
                    'code' => '7202',
                    'unit' => 'EGGD',
                ],
            ]
        );

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => '7203',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItReducesUnitToBaseForm()
    {
        NetworkAircraft::insert(
            [
                [
                    'callsign' => 'VIR25F',
                ],
                [
                    'callsign' => 'BAW92A',
                ],
            ]
        );

        UnitDiscreteSquawkAssignment::insert(
            [
                [
                    'callsign' => 'VIR25F',
                    'code' => '7201',
                    'unit' => 'EGGD',
                ],
                [
                    'callsign' => 'BAW92A',
                    'code' => '7202',
                    'unit' => 'EGGD',
                ],
            ]
        );

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD_APP'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => '7203',
                'created_at' => Carbon::now(),
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
        $this->assertEquals('7201', $this->allocator->allocate('BMI11A', ['unit' => 'EGFF'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => '7201',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItIgnoresOverlappingIfDifferentUnits()
    {
        UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        NetworkAircraft::insert(
            [
                [
                    'callsign' => 'VIR25F',
                ],
                [
                    'callsign' => 'BAW92A',
                ],
            ]
        );

        UnitDiscreteSquawkAssignment::insert(
            [
                [
                    'callsign' => 'VIR25F',
                    'code' => '7201',
                    'unit' => 'EGFF',
                ],
                [
                    'callsign' => 'BAW92A',
                    'code' => '7202',
                    'unit' => 'EGFF',
                ],
            ]
        );

        $this->assertEquals('7201', $this->allocator->allocate('BMI11A', ['unit' => 'EGGD'])->getCode());
        $this->assertDatabaseHas(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'unit' => 'EGGD',
                'code' => '7201',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItFiltersRangesWhereRulesDoNotPass()
    {
        $range = UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

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
        NetworkAircraft::create(
            [
                'callsign' => 'VIR25F',
                'created_at' => Carbon::now(),
            ],
        );

        UnitDiscreteSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'unit' => 'EGGD',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );
        $expected = UnitDiscreteSquawkAssignment::find('VIR25F');

        $this->assertEquals($expected, $this->allocator->fetch('VIR25F'));
    }

    public function testItDeletesAllocations()
    {
        NetworkAircraft::create(
            [
                'callsign' => 'VIR25F',
                'created_at' => Carbon::now(),
            ],
        );

        UnitDiscreteSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'unit' => 'EGGD',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );

        $this->allocator->delete('VIR25F');
        $this->assertDatabaseMissing(
            'unit_discrete_squawk_assignments',
            [
                'callsign' => 'VIR25F',
            ]
        );
    }
}
