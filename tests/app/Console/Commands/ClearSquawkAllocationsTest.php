<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Squawks\Allocation;
use Illuminate\Support\Facades\Artisan;

class ClearSquawkAllocationsTest extends BaseFunctionalTestCase
{
    
    public function testItConstructs()
    {
        $this->assertInstanceOf(ClearSquawkAllocations::class, $this->app->make(ClearSquawkAllocations::class));
    }

    public function testItRemovesAllAllocations()
    {
        $this->assertGreaterThan(0, Allocation::count());
        Artisan::call('allocations:clear');
        $this->assertEquals(0, Allocation::count());
    }
}
