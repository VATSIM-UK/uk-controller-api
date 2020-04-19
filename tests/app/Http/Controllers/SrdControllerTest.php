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

    public function testItRejectsNonNumericRequestedLevel()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => 'abc',
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
                'notes' => [],
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => 'WOTAN L9 KENET',
                'notes' => [],
            ],
            [
                'minimum_level' => 24500,
                'maximum_level' => 66000,
                'route_string' => 'WOTAN UL9 KENET',
                'notes' => [],
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
                'notes' => [],
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => '9000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItFiltersByRequestedLevel()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => 'WOTAN L9 KENET',
                'notes' => [],
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => 'WOTAN L9 KENET',
                'notes' => [],
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => '15000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItFormatsNotes()
    {
        $expected = [
            [
                'minimum_level' => 24500,
                'maximum_level' => 66000,
                'route_string' => 'LISBO DCT RINGA Q39 NOMSU UQ4 WAL UY53 NUGRA',
                'notes' => [
                    'Text 1',
                    'Text 2',
                    'Text 3',
                ]
            ],
        ];

        $queryParams = [
            'origin' => 'EGAA',
            'destination' => 'EGLL',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'srd/route/search', [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }
}
