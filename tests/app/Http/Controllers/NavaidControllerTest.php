<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class NavaidControllerTest extends BaseApiTestCase
{
    public function testItReturnsNavaidDependency()
    {
        $expected = [
            [
                'id' => 1,
                'identifier' => 'WILLO',
            ],
            [
                'id' => 2,
                'identifier' => 'TIMBA',
            ],
            [
                'id' => 3,
                'identifier' => 'MAY',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'navaid/dependency')
            ->assertStatus(200)
            ->assertJson($expected);
    }
}
