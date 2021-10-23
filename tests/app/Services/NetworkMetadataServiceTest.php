<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\Network\NetworkMetadataInvalidException;
use Exception;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NetworkMetadataServiceTest extends BaseFunctionalTestCase
{
    const METADATA_URL = 'https://status.vatsim.net/status.json';

    private NetworkMetadataService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(NetworkMetadataService::class);
        Cache::forget('NETWORK_DATA_URL');
    }

    public function tearDown(): void
    {
        Cache::forget('NETWORK_DATA_URL');
        parent::tearDown();
    }

    public function testItDownloadsNetworkMetadata()
    {
        $this->fakeMetadataRequest(
            [
                'data' => [
                    'v3' => [
                        'https://vatsim.net'
                    ]
                ]
            ]
        );

        $this->assertEquals('https://vatsim.net', $this->service->getNetworkDataUrl());
        $this->assertMetadataRequest();
    }

    public function testItCachesNetworkMetadata()
    {
        $this->fakeMetadataRequest(
            [
                'data' => [
                    'v3' => [
                        'https://vatsim.net'
                    ]
                ]
            ]
        );

        $this->service->getNetworkDataUrl();

        $this->fakeMetadataRequest(
            [
                'data' => [
                    'v3' => [
                        'https://vatsim2.net'
                    ]
                ]
            ]
        );

        $this->assertEquals('https://vatsim.net', $this->service->getNetworkDataUrl());
        $this->assertMetadataRequest();
    }

    public function testItHandlesInvalidHttpResponse()
    {
        $this->fakeMetadataRequest(
            [
                'data' => [
                    'v3' => [
                        'https://vatsim.net'
                    ]
                ]
            ],
            400
        );

        $this->expectException(NetworkMetadataInvalidException::class);
        $this->service->getNetworkDataUrl();
    }

    public function testItHandlesGuzzleException()
    {
        Http::shouldReceive('timeout')->andThrow(new Exception());
        $this->expectException(NetworkMetadataInvalidException::class);
        $this->service->getNetworkDataUrl();
    }

    /**
     * @dataProvider badMetadataProvider
     */
    public function testItHandlesBadMetadataResponse(array $metadata)
    {
        $this->fakeMetadataRequest(
            $metadata
        );

        $this->expectException(NetworkMetadataInvalidException::class);
        $this->service->getNetworkDataUrl();
    }

    public function badMetadataProvider(): array
    {
        return [
            'Data URL is not URL' => [
                [
                    'data' => [
                        'v3' => [
                            'abc',
                        ],
                    ],
                ],
            ],
            'Data URL is not string' => [
                [
                    'data' => [
                        'v3' => [
                            1,
                        ],
                    ],
                ],
            ],
            'Data v3 is empty array' => [
                [
                    'data' => [
                        'v3' => []
                    ],
                ],
            ],
            'Data v3 is not array' => [
                [
                    'data' => [
                        'v3' => ''
                    ],
                ],
            ],
            'Data v3 is not present' => [
                [
                    'data' => [
                        'v4' => ''
                    ],
                ],
            ],
            'Data is not object' => [
                [
                    'data' => ''
                ],
            ],
            'Data is not present' => [
                [
                ],
            ],
            'JSON is not object' => [
                [
                    123,
                    456,
                ],
            ],
        ];
    }

    private function fakeMetadataRequest(array $data, int $responseCode = 200)
    {
        Http::fake(
            [
                self::METADATA_URL => Http::response(json_encode($data), $responseCode)
            ]
        );
    }

    private function assertMetadataRequest()
    {
        Http::assertSent(function (Request $request) {
            return $request->header('User-Agent')[0] === 'UKCP API' &&
                $request->url() === self::METADATA_URL;
        });
    }
}
