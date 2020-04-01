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
        $allocationData = Allocation::find(1)->toArray();
        $allocationData['new'] = false;
        $historyItem = AllocationHistory::create($allocationData);

        $this->assertEquals($allocationData['callsign'], $historyItem->callsign);
        $this->assertEquals($allocationData['squawk'], $historyItem->squawk);
        $this->assertEquals($allocationData['allocated_by'], $historyItem->allocated_by);
        $this->assertEquals($allocationData['allocated_at'], $historyItem->allocated_at->toIso8601ZuluString());
        $this->assertEquals(0, $historyItem->new);
    }
}
