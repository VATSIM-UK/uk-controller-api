<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Metars\Metar;
use Illuminate\Broadcasting\PrivateChannel;

class MetarsUpdatedEventTest extends BaseFunctionalTestCase
{
    private Metar $metar1;
    private Metar $metar2;
    private MetarsUpdatedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->metar1 = Metar::factory()->create();
        $this->metar2 = Metar::factory()->create();
        $this->metar1->refresh();
        $this->metar2->refresh();
        $this->event = new MetarsUpdatedEvent(collect([$this->metar1, $this->metar2]));
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('metar_updates')], $this->event->broadcastOn());
    }
    
    public function testItBroadcastsAsEvent()
    {
        $this->assertEquals("metars.updated", $this->event->broadcastAs());
    }

    public function testItBroadcastsMetarData()
    {
        $expected = [
            [
                'airfield_id' => $this->metar1->airfield_id,
                'raw' => $this->metar1->raw,
                'parsed' => $this->metar1->parsed,
            ],
            [
                'airfield_id' => $this->metar2->airfield_id,
                'raw' => $this->metar2->raw,
                'parsed' => $this->metar2->parsed,
            ],
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }

    public function testItReturnsMetars()
    {
        $this->assertEquals(collect([$this->metar1, $this->metar2]), $this->event->getMetars());
    }
}
