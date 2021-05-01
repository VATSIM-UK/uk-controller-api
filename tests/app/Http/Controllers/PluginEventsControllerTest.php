<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Plugin\PluginEvent;
use App\Services\PluginEventsService;

class PluginEventsControllerTest extends BaseApiTestCase
{
    public function testItReturnsLatestPluginEventId()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'plugin-events/sync')
            ->assertOk()
            ->assertJson(['event_id' => $this->app->make(PluginEventsService::class)->getLatestPluginEventId()]);
    }

    public function testLatestEventsReturnsBadRequestIfNoPreviousProvided()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'plugin-events/recent')
            ->assertStatus(422);
    }

    public function testLatestEventsReturnsBadRequestIfPreviousNotInteger()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'plugin-events/recent?previous=abc')
            ->assertStatus(422);
    }

    public function testLatestEventsReturnsPreviousEvents()
    {
        $event1 = PluginEvent::factory()->create();
        $event2 = PluginEvent::factory()->create();
        $event3 = PluginEvent::factory()->create();

        $expected = [
            [
                'id' => $event2->id,
                'event' => $event2->event,
            ],
            [
                'id' => $event3->id,
                'event' => $event3->event,
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'plugin-events/recent?previous=' . $event1->id)
            ->assertOk()
            ->assertJson($expected);
    }
}
