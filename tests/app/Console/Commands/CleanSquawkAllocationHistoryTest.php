<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Squawks\AllocationHistory;
use Illuminate\Support\Facades\Artisan;

class CleanSquawkAllocationHistoryTest extends BaseFunctionalTestCase
{
    
    public function testItConstructs()
    {
        $this->assertInstanceOf(
            CleanSquawkAllocationHistory::class,
            $this->app->make(CleanSquawkAllocationHistory::class)
        );
    }

    public function testItRemovesAllAllocations()
    {
        $this->assertEquals(2, AllocationHistory::count());
        Artisan::call('allocations:clean-history');
        $this->assertEquals(1, AllocationHistory::count());
    }
}
