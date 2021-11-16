<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Exception;
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
                        'altitude' => '6000',
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
     * @dataProvider badAircraftProvider
     */
    public function testItDoesntReturnInvalidAircraft(array $data)
    {
        $this->dataDownloadService->expects('getNetworkData')->once()->andReturn(
            collect($data)
        );

        $this->assertTrue($this->service->getNetworkAircraftData()->isEmpty());
    }

    public function badAircraftProvider(): array
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

    public function testItReturnsNetworkControllers()
    {
        $expected = collect(
            [
                [
                    'cid' => 1,
                    'callsign' => 'LON_S_CTR',
                    'frequency' => 118.400,
                ],
                [
                    'cid' => 2,
                    'callsign' => 'LON_C_CTR',
                    'frequency' => 199.998,
                ],
            ]
        );

        $this->dataDownloadService->expects('getNetworkData')->once()->andReturn(
            collect([
                        'controllers' => $expected->toArray()
                    ])
        );

        $this->assertEquals($expected, $this->service->getNetworkControllerData());
    }

    /**
     * @dataProvider badControllerProvider
     */
    public function testItDoesntReturnInvalidControllers(array $data)
    {
        $this->dataDownloadService->expects('getNetworkData')->once()->andReturn(
            collect($data)
        );

        $this->assertTrue($this->service->GetNetworkControllerData()->isEmpty());
    }

    public function badControllerProvider(): array
    {
        return [
            'Cid missing' => [
                [
                    'controllers' => [
                        [
                            'callsign' => 'LON_S_CTR',
                            'frequency' => 129.420,
                        ]
                    ],
                ],
            ],
            'Cid not integer' => [
                [
                    'controllers' => [
                        [
                            'cid' => 'abc',
                            'callsign' => 'LON_S_CTR',
                            'frequency' => 129.420,
                        ]
                    ],
                ],
            ],
            'Callsign missing' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'frequency' => 129.420,
                        ]
                    ],
                ],
            ],
            'Callsign invalid' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'callsign' => '[123',
                            'frequency' => 129.420,
                        ]
                    ],
                ],
            ],
            'Frequency missing' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'callsign' => 'LON_S_CTR',
                        ]
                    ],
                ],
            ],
            'Frequency invalid' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'callsign' => 'LON_S_CTR',
                            'frequency' => 'abc',
                        ]
                    ],
                ],
            ],
            'Frequency too big' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'callsign' => 'LON_S_CTR',
                            'frequency' => 200.001,
                        ]
                    ],
                ],
            ],
            'Frequency too small' => [
                [
                    'controllers' => [
                        [
                            'cid' => 1,
                            'callsign' => 'LON_S_CTR',
                            'frequency' => 99.99,
                        ]
                    ],
                ],
            ],
            'Controllers not array' => [
                [
                    'controllers' => ''
                ],
            ],
            'Controllers missing' => [
                [
                ],
            ],
        ];
    }
}
