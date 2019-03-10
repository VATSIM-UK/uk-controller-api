<?php
namespace App\Services;

use App\BaseUnitTestCase;
use App\Helpers\AltimeterSettingRegions\AltimeterSettingRegion;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class RegionalPressureServiceTest extends BaseUnitTestCase
{
    /**
     * @var MetarService
     */
    private $metarService;

    /**
     * Convert an array to XML for utils purposes.
     *
     * @param $array
     * @param $xml
     */
    protected function convertArrayToXml($array, & $xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    $this->convertArrayToXml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("METAR");
                    $this->convertArrayToXml($value, $subnode);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->metarService = new MetarService(new Client());
    }

    public function testItConstructs()
    {
        $service = new RegionalPressureService(new Client(), 'http://test.com', $this->metarService, []);
        $this->assertInstanceOf(RegionalPressureService::class, $service);
    }

    public function testItReturnsFalseWhenProviderReturnsFail()
    {
        $mock = new MockHandler(
            [
            new Response(404, [])
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService($client, 'http://test.com', $this->metarService, []);
        $this->assertFalse($service->generateRegionalPressures());
        $this->assertEquals($service->getLastError(), $service::ERROR_REQUEST_FAILED);
    }

    public function testItReturnsFalseWhenXmlInvalid()
    {
        $mock = new MockHandler(
            [
            new Response(202, [], 'reallyNotXml')
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService($client, 'http://test.com', $this->metarService, []);
        $this->assertFalse($service->generateRegionalPressures());
        $this->assertEquals($service->getLastError(), $service::ERROR_INVALID_XML);
    }

    public function testItCachesEmptyArrayIfNoRegions()
    {
        $fakeXml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $airfieldArray = [
            'data' => [
                ['station_id' => 'EGKK', 'raw_text' => 'EGKK Q1001'],
                ['station_id' => 'EGGD', 'raw_text' => 'EGGD Q1002'],
                ['station_id' => 'EGLL', 'raw_text' => 'EGLL Q1003'],
            ],
        ];
        $this->convertArrayToXml($airfieldArray, $fakeXml);

        $mock = new MockHandler(
            [
            new Response(202, [], $fakeXml->asXML())
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService($client, 'http://test.com', $this->metarService, []);

        Cache::shouldReceive('forever')
            ->once()
            ->with($service::RPS_CACHE_KEY, []);
        $this->assertTrue($service->generateRegionalPressures());
    }

    public function testItReturnsRegionArray()
    {
        $fakeXml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $airfieldArray = [
            'data' => [
                ['station_id' => 'EGKK', 'raw_text' => 'EGKK Q1001'],
                ['station_id' => 'EGGD', 'raw_text' => 'EGGD Q1002'],
                ['station_id' => 'EGLL', 'raw_text' => 'EGLL Q1003'],
                ['station_id' => 'EGFF', 'raw_text' => 'EGFF Q1004'],
            ],
        ];
        $this->convertArrayToXml($airfieldArray, $fakeXml);

        $mock = new MockHandler(
            [
            new Response(202, [], $fakeXml->asXML())
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService(
            $client,
            'http://test.com',
            $this->metarService,
            [
                new AltimeterSettingRegion('Bobbington', 0, ['EGLL', 'EGGD']),
                new AltimeterSettingRegion('Toppington', 0, ['EGFF', 'EGKK']),
            ]
        );

        Cache::shouldReceive('forever')
            ->once()
            ->with(
                $service::RPS_CACHE_KEY,
                [
                    'Bobbington' => '1002',
                    'Toppington' => '1001',
                ]
            );
        $this->assertTrue($service->generateRegionalPressures());
    }

    public function testItHandlesDodgyMetars()
    {
        $fakeXml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $airfieldArray = [
            'data' => [
                ['station_id' => 'EGKK', 'raw_text' => 'EGKK 1001'],
            ],
        ];
        $this->convertArrayToXml($airfieldArray, $fakeXml);

        $mock = new MockHandler(
            [
            new Response(202, [], $fakeXml->asXML())
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService(
            $client,
            'http://test.com',
            $this->metarService,
            [
                new AltimeterSettingRegion('Toppington', 0, ['EGKK']),
            ]
        );

        Cache::shouldReceive('forever')
            ->once()
            ->with(
                $service::RPS_CACHE_KEY,
                [
                    'Toppington' => AltimeterSettingRegion::DEFAULT_MIN_QNH,
                ]
            );
        $this->assertTrue($service->generateRegionalPressures());
    }

    public function testItRetrievesCachedPressures()
    {
        $service = new RegionalPressureService(
            new Client(),
            'http://test.com',
            $this->metarService,
            []
        );

        Cache::shouldReceive('get')
            ->once()
            ->with($service::RPS_CACHE_KEY, [])
            ->andReturn(['Toddington' => 1013]);

        $this->assertEquals(['Toddington' => 1013], $service->getRegionalPressuresFromCache());
    }

    public function testItReturnsEmptyIfCacheReturnsNonArray()
    {
        $service = new RegionalPressureService(
            new Client(),
            'http://test.com',
            $this->metarService,
            []
        );

        Cache::shouldReceive('get')
            ->once()
            ->with($service::RPS_CACHE_KEY, [])
            ->andReturn('Test');

        $this->assertEquals([], $service->getRegionalPressuresFromCache());
    }
}
