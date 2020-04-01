<?php

namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;

class AllocationHistoryTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(AllocationHistory::class, new AllocationHistory());
    }

    public function testItIsMassAssignableFromAllocationDataArray()
    {
        $allocation = Allocation::find(1);
        $historyItem = AllocationHistory::create($allocation->toArray());

        $this->assertEquals($allocation->callsign, $historyItem->callsign);
        $this->assertEquals($allocation->squawk, $historyItem->squawk);
        $this->assertEquals($allocation->allocated_by, $historyItem->allocated_by);
        $this->assertEquals($allocation->allocated_at, $historyItem->allocated_at);
        $this->assertEquals(0, $historyItem->new);
    }
}
