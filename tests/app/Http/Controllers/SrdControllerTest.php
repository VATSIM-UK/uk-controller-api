<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class SrdControllerTest extends BaseApiTestCase
{
    const L9_ROUTE_KENET = 'WOTAN L9 KENET';
    const SRD_SEARCH_URI = 'srd/route/search';
    
    public function testItRejectsMissingOrigin()
    {
        $queryParams = [
            'destination' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonAlphaOrigin()
    {
        $queryParams = [
            'origin' => '123',
            'destination' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsMissingDestination()
    {
        $queryParams = [
            'origin' => 'EGLL'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonAlphaDestination()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => '123'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(400);
    }

    public function testItRejectsNonNumericRequestedLevel()
    {
        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => 'abc',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(400);
    }

    public function testItSearchesByOriginAndDestination()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => self::L9_ROUTE_KENET,
                'notes' => [],
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => 'WOTAN L9 CPT',
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

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testMinimumLevelSearchesIncludeNull()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => self::L9_ROUTE_KENET,
                'notes' => [],
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => '9000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItFiltersByRequestedLevel()
    {
        $expected = [
            [
                'minimum_level' => null,
                'maximum_level' => 28000,
                'route_string' => self::L9_ROUTE_KENET,
                'notes' => [],
            ],
            [
                'minimum_level' => 10000,
                'maximum_level' => 19500,
                'route_string' => self::L9_ROUTE_KENET,
                'notes' => [],
            ],
        ];

        $queryParams = [
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'requestedLevel' => '15000'
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItAppendsDestinationIfNotAirport()
    {
        $expected = [
            [
                'minimum_level' => 24500,
                'maximum_level' => 66000,
                'route_string' => 'MID UL612 VEULE',
                'notes' => [],
            ],
        ];

        $queryParams = [
            'origin' => 'EGLL',
            'destination' => 'VEULE',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
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
                    [
                        'id' => 1,
                        'text' => 'Text 1',
                    ],
                    [
                        'id' => 2,
                        'text' => 'Text 2',
                    ],
                    [
                        'id' => 3,
                        'text' => 'Text 3',
                    ],
                ]
            ],
        ];

        $queryParams = [
            'origin' => 'EGAA',
            'destination' => 'EGLL',
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SRD_SEARCH_URI, [], $queryParams)
            ->assertStatus(200)
            ->assertJson($expected);
    }
}
