<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\HandoffService;

class HandoffControllerTest extends BaseApiTestCase
{
    public function testItGetsHandoffV2Dependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoffs/dependency')
            ->assertJson($this->app->make(HandoffService::class)->getHandoffsV2Dependency());
    }

    public function testItGetsHandoffV2Dependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoffs/dependency')
            ->assertJson($this->app->make(HandoffService::class)->getHandoffsV2Dependency());
    }
}
