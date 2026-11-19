<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\RunwayService;

class RunwayControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(RunwayController::class, $this->app->make(RunwayController::class));
    }

    public function testItReturnsRunwaysDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'runway/dependency')
            ->assertOk()
            ->assertJson($this->app->make(RunwayService::class)->getRunwaysDependency());
    }
}
