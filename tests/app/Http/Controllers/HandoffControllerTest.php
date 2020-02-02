<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

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
}
