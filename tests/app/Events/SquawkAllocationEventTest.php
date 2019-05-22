<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Squawks\Allocation;

class SquawkAllocationEventTest extends BaseFunctionalTestCase
{
    /**
     * @var SquawkAllocationEvent
     */
    private $event;

    /**
     * @var Allocation
     */
    private $allocation;

    public function setUp() : void
    {
        parent::setUp();
        $this->allocation = Allocation::find(1);
        $this->event = new SquawkAllocationEvent($this->allocation);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkAllocationEvent::class, $this->event);
    }

    public function testItSerializesAllocationsToArray()
    {
        $expected = array_merge(
            $this->allocation->toArray(),
            ['new' => false]
        );

        $this->assertEquals(
            $expected,
            $this->event->allocation()
        );
    }
}
