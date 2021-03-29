<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\WakeService;

class WakeControllerTest extends BaseApiTestCase
{
    private WakeService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(WakeService::class);
    }

    public function testItReturnsWakeDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'wake-schemes/dependency')
            ->assertOk()
            ->assertJson($this->service->getWakeSchemesDependency());
    }
}
