<?php
namespace App\Services;

use App\BaseUnitTestCase;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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

    public function setUp() : void
    {
        parent::setUp();
        $this->metarService = new MetarService(new Client());
    }

    public function testItConstructs()
    {
        $service = new RegionalPressureService(new Client(), 'http://test.com', $this->metarService);
        $this->assertInstanceOf(RegionalPressureService::class, $service);
    }

    public function testItReturnsNullWhenProviderReturnsFail()
    {
        $mock = new MockHandler(
            [
                new Response(404, [])
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService($client, 'http://test.com', $this->metarService);
        $this->assertNull($service->generateRegionalPressures());
        $this->assertEquals($service->getLastError(), $service::ERROR_REQUEST_FAILED);
    }

    public function testItReturnsNullWhenXmlInvalid()
    {
        $mock = new MockHandler(
            [
                new Response(202, [], 'reallyNotXml')
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService($client, 'http://test.com', $this->metarService);
        $this->assertNull($service->generateRegionalPressures());
        $this->assertEquals($service->getLastError(), $service::ERROR_INVALID_XML);
    }

    public function testItReturnsRegionArray()
    {
        $fakeXml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $airfieldArray = [
            'data' => [
                ['station_id' => 'EGKR', 'raw_text' => 'EGKR Q1001'],
                ['station_id' => 'EGBB', 'raw_text' => 'EGBB Q1002'],
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


        $service = new RegionalPressureService(
            $client,
            'http://test.com',
            $this->metarService
        );

        $expected = [
            'ASR_BOBBINGTON' => 1000,
            'ASR_TOPPINGTON' => 1001,
        ];
        $this->assertEquals($expected, $service->generateRegionalPressures());
    }

    public function testItHandlesDodgyMetars()
    {
        $fakeXml = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $airfieldArray = [
            'data' => [
                ['station_id' => 'EGLL', 'raw_text' => 'EGKK 1001'],
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
            $this->metarService
        );

        $expected = [
            'ASR_BOBBINGTON' => $service::LOWEST_QNH_DEFAULT,
            'ASR_TOPPINGTON' => $service::LOWEST_QNH_DEFAULT,
        ];
        $this->assertEquals($expected, $service->generateRegionalPressures());
    }

    public function testItCalculatesRegionalPressureAsLowestOfAirfieldsSubtractOne()
    {
        $expected = 999;
        $airfieldQnhs = [
            'EGLL' => 1000,
            'EGKR' => 1001
        ];

        $mock = new MockHandler(
            [
                new Response(202, [], '')
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService(
            $client,
            'http://test.com',
            $this->metarService
        );

        $this->assertEquals(
            $expected,
            $service->calculateRegionalPressure(AltimeterSettingRegion::findOrFail(1), $airfieldQnhs)
        );
    }

    public function testItReturnsDefaultQnhIfAirfieldNotFound()
    {
        $expected = RegionalPressureService::LOWEST_QNH_DEFAULT;
        $airfieldQnhs = [
            'EGLL' => 1000,
            'EGKR' => 1001
        ];

        $mock = new MockHandler(
            [
                new Response(202, [], '')
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);


        $service = new RegionalPressureService(
            $client,
            'http://test.com',
            $this->metarService
        );

        $this->assertEquals(
            $expected,
            $service->calculateRegionalPressure(AltimeterSettingRegion::findOrFail(2), $airfieldQnhs)
        );
    }
}
