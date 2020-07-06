<?php

namespace App\Providers;

use App\BaseUnitTestCase;
use App\Events\SquawkAssignmentEvent;
use App\Listeners\Squawk\RecordSquawkAssignmentHistory;

class EventServiceProviderTest extends BaseUnitTestCase
{
    /**
     * @var EventServiceProvider
     */
    private $provider;

    public function setUp() : void
    {
        parent::setUp();
        $this->provider = $this->app->make(EventServiceProvider::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(EventServiceProvider::class, $this->provider);
    }

    public function testItListensForSquawkAllocations()
    {
        $this->assertEquals(
            [RecordSquawkAssignmentHistory::class],
            $this->provider->listens()[SquawkAssignmentEvent::class]
        );
    }
}
