<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\PrenoteService;

class PrenoteControllerTest extends BaseApiTestCase
{
    public function testItReturnsPrenotesV2Dependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'prenotes/dependency')
            ->assertStatus(200)
            ->assertExactJson($this->app->make(PrenoteService::class)->getPrenotesV2Dependency());
    }
}
