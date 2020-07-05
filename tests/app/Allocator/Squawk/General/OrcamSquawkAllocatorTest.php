<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAllocationCategories;
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
        OrcamSquawkRange::create(
            [
                'origin' => 'E',
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

        OrcamSquawkAssignment::insert(
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

        $this->assertEquals(
            '7203',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7203',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItAllocatesSingleCharacterRange()
    {
        OrcamSquawkRange::create(
            [
                'origin' => 'E',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        $this->assertEquals(
            '7201',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7201',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItPrefersDoubleCharacterMatchOverSingle()
    {
        OrcamSquawkRange::insert(
            [
                [
                    'origin' => 'E',
                    'first' => '7201',
                    'last' => '7201',
                ],
                [
                    'origin' => 'ED',
                    'first' => '7202',
                    'last' => '7202',
                ]
            ]
        );

        $this->assertEquals(
            '7202',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7202',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItPrefersTripleCharacterMatchOverDouble()
    {
        OrcamSquawkRange::insert(
            [
                [
                    'origin' => 'E',
                    'first' => '7201',
                    'last' => '7201',
                ],
                [
                    'origin' => 'ED',
                    'first' => '7202',
                    'last' => '7202',
                ],
                [
                    'origin' => 'EDD',
                    'first' => '7203',
                    'last' => '7203',
                ]
            ]
        );

        $this->assertEquals(
            '7203',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7203',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItPrefersFullMatch()
    {
        OrcamSquawkRange::insert(
            [
                [
                    'origin' => 'E',
                    'first' => '7201',
                    'last' => '7201',
                ],
                [
                    'origin' => 'ED',
                    'first' => '7202',
                    'last' => '7202',
                ],
                [
                    'origin' => 'EDD',
                    'first' => '7203',
                    'last' => '7203',
                ],
                [
                    'origin' => 'EDDF',
                    'first' => '7204',
                    'last' => '7204',
                ],
            ]
        );

        $this->assertEquals(
            '7204',
            $this->allocator->allocate('BMI11A', ['origin' => 'EDDF'])->getCode()
        );
        $this->assertDatabaseHas(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7204',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItReturnsNullOnNoApplicableRange()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGGD']));
    }

    public function testItReturnsNullOnMissingOrigin()
    {
        $this->assertNull($this->allocator->allocate('BMI11A', []));
        $this->assertDatabaseMissing(
            'orcam_squawk_assignments',
            [
                'callsign' => 'BMI11A',
            ]
        );
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

        OrcamSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );
        $expected = OrcamSquawkAssignment::find('VIR25F');

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

        OrcamSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );

        $this->assertTrue($this->allocator->delete('VIR25F'));
        $this->assertDatabaseMissing(
            'orcam_squawk_assignments',
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
