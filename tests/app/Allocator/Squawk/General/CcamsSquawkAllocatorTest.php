<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentCategories;
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
        CcamsSquawkRange::query()->delete();
    }

    public function testItAllocatesFirstFreeSquawkInRange()
    {
        $this->createSquawkRange('7201', '7210');
        $this->createSquawkAssignment('VIR25F', '7201');
        $this->createSquawkAssignment('BAW92A', '7202');

        $this->assertEquals('7203', $this->allocator->allocate('BMI11A', [])->getCode());
        $this->assertSquawkAssigned('BMI11A', '7203');
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
        $this->createSquawkAssignment('VIR25F', '0001');
        $expected = CcamsSquawkAssignment::find('VIR25F');

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
        string $first,
        string $last
    ) {
        CcamsSquawkRange::create(
            [
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
        CcamsSquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $code,
            ]
        );
    }

    private function assertSquawkNotAsssigned(string $callsign)
    {
        $this->assertDatabaseMissing(
            'ccams_squawk_assignments',
            [
                'callsign' => $callsign
            ]
        );
    }

    private function assertSquawkAssigned(string $callsign, string $code)
    {
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => $callsign,
                'code' => $code,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItAssignsToCallsignIfFree()
    {
        $this->createSquawkRange('0001', '0007');
        $this->assertEquals('0002', $this->allocator->assignToCallsign('0002', 'RYR111')->getCode());
        $this->assertSquawkAssigned('RYR111', '0002');
    }

    public function testItDoesntAssignIfNotInRange()
    {
        $this->createSquawkRange('0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0010'));
        $this->assertSquawkNotAsssigned('RYR111');
    }

    public function testItDoesntAssignIfAlreadyAssigned()
    {
        $this->createSquawkAssignment('RYR234', '0001');
        $this->createSquawkRange('0001', '0007');
        $this->assertNull($this->allocator->assignToCallsign('RYR111', '0001'));
        $this->assertSquawkNotAsssigned('RYR111');
        $this->assertSquawkAssigned('RYR234', '0001');
    }
}
