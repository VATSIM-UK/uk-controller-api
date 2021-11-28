<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\HandoffService;

class HandoffControllerTest extends BaseApiTestCase
{
    public function testItGetsHandoffs()
    {
        $expected = [
            'HANDOFF_ORDER_1' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ],
            'HANDOFF_ORDER_2' => [
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoff')
            ->assertJson($expected);
    }

    public function testItGetsHandoffV2Dependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoffs/dependency')
            ->assertJson($this->app->make(HandoffService::class)->getHandoffsV2Dependency());
    }
}
