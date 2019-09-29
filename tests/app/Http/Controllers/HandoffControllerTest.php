<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class HandoffControllerTest extends BaseApiTestCase
{
    public function testItGetsHandoffs()
    {
        $expected = [
            [
                "id" => 1,
                "key" => "HANDOFF_ORDER_1",
                "description" => "foo",
                "controllers" => [
                    1,
                    2,
                ],
            ],
            [
                "id" => 2,
                "key" => "HANDOFF_ORDER_2",
                "description" => "foo",
                "controllers" => [
                    2,
                    3,
                ],
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoff')
            ->assertJson($expected);
    }
}
