<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

class PrenoteServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var PrenoteService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PrenoteService::class);
    }

    public function testItFormatsSidPrenotes()
    {
        $expected = [
            [
                'airfield' => 'EGLL',
                'departure' => 'TEST1X',
                'type' => 'sid',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllSidPrenotes());
    }

    public function testItFormatsAirfieldPairingPrenotes()
    {
        $expected = [
            [
                'origin' => 'EGLL',
                'destination' => 'EGBB',
                'type' => 'airfieldPairing',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllAirfieldPrenotes());
    }

    public function testItFormatsAllPrenotes()
    {
        $expected = [
            [
                'airfield' => 'EGLL',
                'departure' => 'TEST1X',
                'type' => 'sid',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ],
            [
                'origin' => 'EGLL',
                'destination' => 'EGBB',
                'type' => 'airfieldPairing',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllPrenotesWithControllers());
    }
}
