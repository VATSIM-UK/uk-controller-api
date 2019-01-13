<?php

namespace App\Providers;

use App\BaseUnitTestCase;
use App\Events\SquawkAllocationEvent;
use App\Listeners\Squawk\RecordSquawkAllocationHistory;

class EventServiceProviderTest extends BaseUnitTestCase
{
    /**
     * @var EventServiceProvider
     */
    private $provider;

    public function setUp()
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
            [RecordSquawkAllocationHistory::class],
            $this->provider->listens()[SquawkAllocationEvent::class]
        );
    }
}
