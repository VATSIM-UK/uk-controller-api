<?php

namespace App\Events;

use App\BaseUnitTestCase;
use Illuminate\Broadcasting\PrivateChannel;

class MinStacksUpdatedEventTest extends BaseUnitTestCase
{
    /**
     * @var MinStacksUpdatedEvent
     */
    private $event;

    public function setUp() : void
    {
        parent::setUp();
        $airfields = [
            'EGKK' => 7000,
        ];
        $tmas = [
            'LTMA' => 7000,
        ];
        $this->event = new MinStacksUpdatedEvent($airfields, $tmas);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MinStacksUpdatedEvent::class, $this->event);
        $this->assertEquals(['EGKK' => 7000], $this->event->airfield);
        $this->assertEquals(['LTMA' => 7000], $this->event->tma);
    }

    public function testItBroadcastsOnTheCorrectChannel()
    {
        $this->assertEquals([new PrivateChannel($this->event::CHANNEL)], $this->event->broadcastOn());
    }
}
