<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAllocationCategories;
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

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGHH',
                'first' => '7251',
                'last' => '7257',
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

        AirfieldPairingSquawkAssignment::insert(
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
            $this->allocator->allocate(
                'BMI11A',
                ['origin' => 'EGGD', 'destination' => 'EGFF']
            )->getCode()
        );
        $this->assertDatabaseHas(
            'airfield_pairing_squawk_assignments',
            [
                'callsign' => 'BMI11A',
                'code' => '7203',
                'created_at' => Carbon::now(),
            ]
        );
    }

    public function testItReturnsNullNoApplicableRange()
    {
        AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGGD', 'destination' => 'EGKK']));
    }

    public function testItReturnsNullMissingOrigin()
    {
        AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        $this->assertNull($this->allocator->allocate('BMI11A', ['destination' => 'EGFF']));
    }

    public function testItReturnsNullMissingDestination()
    {
        AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '7201',
                'last' => '7210',
            ]
        );

        $this->assertNull($this->allocator->allocate('BMI11A', ['origin' => 'EGFF']));
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

        AirfieldPairingSquawkAssignment::create(
            [
                'callsign' => 'VIR25F',
                'code' => '0001',
                'created_at' => Carbon::now(),
            ],
        );
        $expected = AirfieldPairingSquawkAssignment::find('VIR25F');

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

        AirfieldPairingSquawkAssignment::create(
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
