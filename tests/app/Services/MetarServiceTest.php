<?php
namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class MetarServiceTest extends BaseFunctionalTestCase
{
    const URL_CONFIG_KEY = 'metar.vatsim_url';

    private MetarService $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(MetarService::class);
    }

    public function testMetarsPreferTheQnhOverAltimeter()
    {
        $this->expectsEvents(MetarsUpdatedEvent::class);
        Metar::create(['airfield_id' => 1, 'metar_string' => 'bla']);
        $noPressureAirfield = Airfield::factory()->make();
        $noMetarAirfield = Airfield::factory()->make();

        $dataResponse = [
            'EGLL Q1001', // Will pull through the QNH as 1001
            'EGBB Q0991 A2992', // Will pull through the QNH as 991
            'EGKR A2992', // Will pull through altimeter and convert it to QNH,
            $noPressureAirfield->code, // Wont pull through a QNH as there is none.
        ];

        Http::fake(
            [
                config(self::URL_CONFIG_KEY) => Http::response(implode("\n", $dataResponse), 200),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                $request->url() === config(self::URL_CONFIG_KEY) &&
                $request['id'] === 'EGLL,EGBB,EGKR';
        });

        // Check the metars are in the database
        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 1,
                'raw' => 'EGLL Q1001',
                'qnh' => 1001,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 2,
                'raw' => 'EGLL Q0991',
                'qnh' => 991,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 3,
                'raw' => 'EGKR A2992',
                'qnh' => 1013,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => $noPressureAirfield->id,
                'raw' => $noPressureAirfield->code,
                'qnh' => null,
            ]
        );

        $this->assertDatabaseMissing(
            'metars',
            [
                'airfield_id' => $noMetarAirfield->id,
            ]
        );
    }

    public function testItHandlesBadResponsesGracefully()
    {
        $this->doesntExpectEvents(MetarsUpdatedEvent::class);
        Http::fake(
            [
                config(self::URL_CONFIG_KEY) => Http::response('', 500),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                $request->url() === config(self::URL_CONFIG_KEY) &&
                $request['id'] === 'EGLL,EGBB,EGKR';
        });

        // Check the metars aren't there
        $this->assertDatabaseMissing(
            'metars',
            [
                'airfield_id' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'metars',
            [
                'airfield_id' => 2,
            ]
        );
        $this->assertDatabaseMissing(
            'metars',
            [
                'airfield_id' => 3,
            ]
        );
    }
}
