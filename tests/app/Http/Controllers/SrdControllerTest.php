<?php

namespace app\Http\Controllers;

use App\BaseApiTestCase;

class SrdControllerTest extends BaseApiTestCase
{
    public function testItRejectsMissingOrigin()
    {
        $queryParams = [
            'destination' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonAlphaOrigin()
    {
        $queryParams = [
            'origin' => '123',
            'destination' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsMissingDestination()
    {
        $queryParams = [
            'origin' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonAlphaDestination()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => '123'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonNumericMinLevel()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'minLevel' => 'abc',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonNumericMasLevel()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'maxLevel' => 'abc',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(400);
    }

    public function testItSearchesByOriginAndDestination()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => 'WOTAN L9 KENET',
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => 'WOTAN L9 KENET',
            ],
            [
                'minimum_level' => 24500,
                'maximum_level' => 66000,
                'route_string' => 'WOTAN UL9 KENET',
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testMinimumLevelSearchesIncludeNull()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => 'WOTAN L9 KENET',
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'minLevel' => '9000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItFiltersByMinimumLevel()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => 'WOTAN L9 KENET',
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => 'WOTAN L9 KENET',
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'minLevel' => '22000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItFiltersByMaximumLevel()
    {
        $expected = [
            [
                'minimum_level' => 24500,
                'maximum_level' => 66000,
                'route_string' => 'WOTAN UL9 KENET',
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'maxLevel' => '30000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }
}
