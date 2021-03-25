<?php

namespace App\Listeners;

use App\BaseUnitTestCase;

class HighPriorityTest extends BaseUnitTestCase
{
    use HighPriority;

    public function testItSetsTheQueue()
    {
        $this->assertEquals('high', $this->viaQueue());
    }
}
