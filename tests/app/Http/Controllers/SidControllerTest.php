<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\SidService;

class SidControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(SidController::class, $this->app->make(SidController::class));
    }

    public function testItReturnsSidsDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid/dependency')
            ->assertOk()
            ->assertJson($this->app->make(SidService::class)->getSidsDependency());
    }
}
