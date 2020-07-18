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

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        $this->createSquawkRange('EGGD', 'EGFF', '7201', '7210');
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
        $this->assertSquawkAsssigned('BMI11A', '7203');
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
            [SquawkAssignmentCategories::GENERAL, true],
            [SquawkAssignmentCategories::LOCAL, false],
        ];
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

    private function assertSquawkAsssigned(string $callsign, string $code)
    {
        $this->assertDatabaseMissing(
            'airfield_pairing_squawk_assignments',
            [
                'callsign' => $callsign,
                'code' => $code,
                'created_at' => Carbon::now()->startOfSecond(),
            ]
        );
    }
}
