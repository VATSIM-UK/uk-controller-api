<?php
namespace App\Services;

use App\BaseUnitTestCase;
use App\Exceptions\MetarException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;
use Mockery;

class MetarServiceTest extends BaseUnitTestCase
{
    /**
     * @var MetarService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(MetarService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MetarService::class, $this->service);
    }

    public function testItThrowsAnExceptionIfNoQnh()
    {
        $metar = 'EGGD BKN100';
        $this->expectException(MetarException::class);
        $this->expectExceptionMessage('QNH not found in METAR: ' . $metar);

        $this->service->getQnhFromMetar($metar);
    }

    public function testItFindsAFourDigitQNH()
    {
        $metar = 'EGGD Q1001';
        $this->assertEquals(1001, $this->service->getQnhFromMetar($metar));
    }

    public function testItFindsAThreeDigitQNH()
    {
        $metar = 'EGGD Q0998';
        $this->assertEquals(998, $this->service->getQnhFromMetar($metar));
    }

    public function testItUsesTheFirstQNHPresent()
    {
        $metar = 'EGGD Q1029 Q1001';
        $this->assertEquals(1029, $this->service->getQnhFromMetar($metar));
    }

    public function testItReturnsNullIfVatsimMetarDownloadFails()
    {
        $mockResponse = new Response(418, []);

        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
            ->with(
                env('VATSIM_METAR_URL'),
                [
                    RequestOptions::ALLOW_REDIRECTS => true,
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::QUERY => [
                        'id' => 'EGLL',
                    ],
                ]
            )
            ->andReturn($mockResponse);

        $service = new MetarService($mockClient);
        $this->assertNull($service->getQnhFromVatsimMetar('EGLL'));
    }

    public function testItReturnsNullIfNoMetarAvailable()
    {
        $mockResponse = new Response(200, [], 'No METAR available for EGLL');

        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
            ->with(
                env('VATSIM_METAR_URL'),
                [
                    RequestOptions::ALLOW_REDIRECTS => true,
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::QUERY => [
                        'id' => 'EGLL',
                    ],
                ]
            )
            ->andReturn($mockResponse);

        $service = new MetarService($mockClient);
        $this->assertNull($service->getQnhFromVatsimMetar('EGLL'));
    }

    public function testItReturnsNullIfMetarNotValid()
    {
        $mockResponse = new Response(200, [], 'EGLL 1234567');

        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
            ->with(
                env('VATSIM_METAR_URL'),
                [
                    RequestOptions::ALLOW_REDIRECTS => true,
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::QUERY => [
                        'id' => 'EGLL',
                    ],
                ]
            )
            ->andReturn($mockResponse);

        $service = new MetarService($mockClient);
        $this->assertNull($service->getQnhFromVatsimMetar('EGLL'));
    }

    public function testItReturnsQnhIfValidMetar()
    {
        $mockResponse = new Response(200, [], 'EGLL 02012KT Q1014');

        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
            ->with(
                env('VATSIM_METAR_URL'),
                [
                    RequestOptions::ALLOW_REDIRECTS => true,
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::QUERY => [
                        'id' => 'EGLL',
                    ],
                ]
            )
            ->andReturn($mockResponse);

        $service = new MetarService($mockClient);
        $this->assertEquals(1014, $service->getQnhFromVatsimMetar('EGLL'));
    }
}
