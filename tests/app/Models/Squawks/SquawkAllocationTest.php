<?php
namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class SquawkAllocationTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(Allocation::class, $this->app->make(Allocation::class));
    }

    public function testItCanCreateANewAllocation()
    {
        $data = [
            'callsign' => 'TCX1AX',
            'squawk' => "1234",
            'allocated_by' => self::ACTIVE_USER_CID,
            'allocated_at' => Carbon::now()->subHour()
        ];
        $allocation = Allocation::create($data);

        $this->seeInDatabase("squawk_allocation", $data);
    }

    public function testAllocatedCanBeTouched()
    {
        $data = [
            'callsign' => 'TCX1AX',
            'squawk'=>"1234",
            'allocated_by' => self::ACTIVE_USER_CID,
            'allocated_at' => Carbon::now()->subHour()
        ];
        $allocation = Allocation::create($data);

        Carbon::setTestNow(Carbon::now()); // Set the carbon object to return a constant now for testings

        $allocation->touchAllocated();

        $this->assertEquals($allocation->allocated_at, Carbon::now());
        Carbon::setTestNow(); // Clear mock
    }
}
