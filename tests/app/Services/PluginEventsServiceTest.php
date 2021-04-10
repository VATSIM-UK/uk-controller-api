<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Plugin\PluginEvent;

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
}
