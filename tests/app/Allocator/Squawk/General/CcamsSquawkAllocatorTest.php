<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAllocationCategories;
use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class CcamsSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var CcamsSquawkAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = new CcamsSquawkAllocator();
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        CcamsSquawkRange::create(
            [
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

        CcamsSquawkAssignment::insert(
            [
                [
                    'callsign' => 'VIR25F',
                    'code' => '7201',
                ],
                [
                    'callsign' => 'BAW92A',
                    'code' => '7202',
                ],
            ]
        );

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', [])->getCode());
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7203',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItReturnsNullOnNoApplicableRange()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', []));
    }

    public function testItReturnsNullIfAllocationFails()
    {
        $this->assertNull($this->allocator->allocate('BAW123', []));
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

        CcamsSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );
        $expected = CcamsSquawkAssignment::find('VIR25F');

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

        CcamsSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );

        $this->assertTrue($this->allocator->delete('VIR25F'));
        $this->assertDatabaseMissing(
            'ccams_squawk_assignments',
            [
                'callsign' => 'VIR25F'
            ]
        );
    }

    public function testItReturnsFalseForNonDeletedAllocations()
    {
        $this->assertFalse($this->allocator->delete('LALALA'));
    }


    /**
     * @dataProvider categoryProvider
     */
    public function testItAllocatesCategories(string $category, bool $expected)
    {
        $this->assertEquals($expected, $this->allocator->canAllocateForCategory($category));
    }

    public function categoryProvider(): array
    {
        return [
            [SquawkAllocationCategories::CATEGORY_GENERAL, true],
            [SquawkAllocationCategories::CATEGORY_LOCAL, false],
        ];
    }
}
