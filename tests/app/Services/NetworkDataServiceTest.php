<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Exception;
use Illuminate\Support\Collection;
use Mockery;

class NetworkDataServiceTest extends BaseFunctionalTestCase
{
    private NetworkDataDownloadService $dataDownloadService;
    private NetworkDataService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataDownloadService = Mockery::mock(NetworkDataDownloadService::class);
        $this->app->instance(NetworkDataDownloadService::class, $this->dataDownloadService);
        $this->service = $this->app->make(NetworkDataService::class);
    }

    public function testItReturnsNetworkAircraft()
    {
        $expected = collect(
            [
                'pilots' => [
                    [
                        'callsign' => 'BAW123',
                        'latitude' => 34,
                        'longitude' => 45,
                        'altitude' => 3000,
                        'groundspeed' => 300,
                        'transponder' => '1234',
                        'flight_plan' => [
                            'aircraft' => 'B738',
                            'departure' => 'EGLL',
                            'arrival' => 'EGSS',
                            'altitude' => 6000,
                            'flight_rules' => 'I',
                            'route' => 'DCT BKY',
                        ],
                    ],
                    [
                        'callsign' => 'BAW123',
                        'latitude' => 34,
                        'longitude' => 45,
                        'altitude' => 3000,
                        'groundspeed' => 300,
                        'transponder' => '1234',
                        'flight_plan' => null,
                    ],
                ],
            ]
        );

        $this->dataDownloadService->expects('getNetworkData')->once()->andReturn(
            collect([
                'pilots' => $expected->toArray()
            ])
        );

        $this->assertEquals($expected, $this->service->getNetworkAircraftData());
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItDoesntReturnInvalidAircraft(array $data)
    {
        $this->dataDownloadService->expects('getNetworkData')->once()->andReturn(
            collect($data)
        );

        $this->assertTrue($this->service->getNetworkAircraftData()->isEmpty());
    }

    public function badDataProvider(): array
    {
        return [
            'No pilots' => [
                [
                    'notpilots' => [],
                ]
            ],
            'Pilots not array' => [
                [
                    'pilots' => ''
                ]
            ],
            'Callsign invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 123,
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Callsign missing' => [
                [
                    'pilots' => [
                        [
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Latitude invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 999,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Latitude missing' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Longitude invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 999,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Longitude missing' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Altitude invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 'abc',
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Altitude missing' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Groundspeed invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 'abc',
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Groundspeed missing' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Transponder invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => 'abc',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Transponder missing' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan aircraft invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => '1234',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan departure invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => '1234',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan arrival invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 123,
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan altitude invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => new Exception(),
                                'flight_rules' => 'I',
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan flight rules invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 123,
                                'route' => 'DCT BKY',
                            ],
                        ]
                    ]
                ]
            ],
            'Flightplan route invalid' => [
                [
                    'pilots' => [
                        [
                            'callsign' => 'BAW123',
                            'latitude' => 34,
                            'longitude' => 45,
                            'altitude' => 3000,
                            'groundspeed' => 300,
                            'transponder' => '1234',
                            'flight_plan' => [
                                'aircraft' => 'B738',
                                'departure' => 'EGLL',
                                'arrival' => 'EGSS',
                                'altitude' => 6000,
                                'flight_rules' => 'I',
                                'route' => 123,
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }
}
