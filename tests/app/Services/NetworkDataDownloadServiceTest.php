<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Exception;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery;

class NetworkDataDownloadServiceTest extends BaseFunctionalTestCase
{
    const DATA_URL = 'https://data.vatsim.net/v3/vatsim-data.json';
    const METADATA_URL = 'https://status.vatsim.net/status.json';

    private NetworkDataDownloadService $service;
    private NetworkMetadataService $mockMetadataService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockMetadataService = Mockery::mock(NetworkMetadataService::class);
        $this->service = new NetworkDataDownloadService($this->mockMetadataService);
    }

    public function testItDownloadsNetworkData()
    {
        $this->fakeDataAndMetadataRequest(
            [
                'foo' => 'bar',
            ]
        );

        $this->assertEquals(collect(['foo' => 'bar']), $this->service->getNetworkData());
        $this->assertDataRequestSent();
    }

    public function testItCachesNetworkData()
    {
        $this->fakeDataAndMetadataRequest(
            [
                'foo' => 'bar',
            ]
        );

        $this->assertEquals(collect(['foo' => 'bar']), $this->service->getNetworkData());
        $this->assertDataRequestSent();

        $this->fakeDataRequest(
            [
                'bish' => 'bash',
            ]
        );

        $this->assertEquals(collect(['foo' => 'bar']), $this->service->getNetworkData());
        Http::assertSentCount(0);
    }

    public function testItHandlesUnsuccessfulResponseFromNetworkData()
    {
        $this->fakeDataAndMetadataRequest(
            [
                'foo' => 'bar',
            ],
            400
        );

        $this->assertEquals(collect(), $this->service->getNetworkData());
        $this->assertDataRequestSent();
    }

    public function testItHandlesExceptionFromMetadata()
    {
        $this->mockMetadataService->shouldReceive('getNetworkDataUrl')
            ->once()
            ->andThrow(new Exception());

        $this->assertEquals(collect(), $this->service->getNetworkData());
        $this->assertDataRequestNotSent();
    }

    private function fakeDataRequest(array $data, int $responseCode = 200)
    {
        Http::fake(
            [
                self::DATA_URL => Http::response(json_encode($data), $responseCode)
            ]
        );
    }

    private function fakeDataAndMetadataRequest(array $data, int $responseCode = 200)
    {
        $this->mockMetadataService->shouldReceive('getNetworkDataUrl')
            ->once()
            ->andReturn(self::DATA_URL);

        $this->fakeDataRequest($data, $responseCode);
    }

    private function assertDataRequestSent()
    {
        Http::assertSent(function (Request $request) {
            return $request->header('User-Agent')[0] === 'UKCP API' &&
                $request->url() === self::DATA_URL;
        });
    }

    private function assertDataRequestNotSent()
    {
        Http::assertNothingSent();
    }
}
