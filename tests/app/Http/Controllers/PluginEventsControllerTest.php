<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\PluginEventsService;

class PluginEventsControllerTest extends BaseApiTestCase
{
    public function testItReturnsLatestPluginEventId()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'plugin-events/sync')
            ->assertOk()
            ->assertJson(['event_id' => $this->app->make(PluginEventsService::class)->getLatestPluginEventId()]);
    }
}
