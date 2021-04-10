<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Plugin\PluginEvent;
use Illuminate\Support\Collection;

class PluginEventsServiceTest extends BaseFunctionalTestCase
{
    private PluginEventsService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PluginEventsService::class);
    }

    public function testItReturnsZeroIfNoPluginEvents()
    {
        $this->assertEquals(0, $this->service->getLatestPluginEventId());
    }

    public function testItReturnsTheLatestPluginEventId()
    {
        PluginEvent::factory()->create();
        PluginEvent::factory()->create();
        PluginEvent::factory()->create();
        PluginEvent::factory()->create();
        PluginEvent::factory()->create();
        $latestEvent = PluginEvent::create(['event' => ['foo' => 'bar']]);
        $this->assertEquals($latestEvent->id, $this->service->getLatestPluginEventId());
    }

    public function testItReturnsRecentPluginEvents()
    {
        $event1 = PluginEvent::factory()->create();
        $event2 = PluginEvent::factory()->create();
        $event3 = PluginEvent::factory()->create();

        $expected = new Collection(
            [
                [
                    'id' => $event2->id,
                    'event' => $event2->event,
                ],
                [
                    'id' => $event3->id,
                    'event' => $event3->event,
                ],
            ]
        );
        $this->assertEquals($expected, $this->service->getRecentPluginEvents($event1->id));
    }
}
