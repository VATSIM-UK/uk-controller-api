<?php
namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        Metar::create(['airfield_id' => 1, 'raw' => 'bla']);
        $noPressureAirfield = Airfield::factory()->create();
        $noMetarAirfield = Airfield::factory()->create();

        $dataResponse = [
            'EGLL Q1001', // Will pull through the QNH as 1001
            'EGBB Q0991 A2992', // Will pull through the QNH as 991
            'EGKR A2992', // Will pull through altimeter and convert it to QNH,
            $noPressureAirfield->code, // Wont pull through a QNH as there is none.
        ];


        $expectedUrl = config(self::URL_CONFIG_KEY) . '?id=' . urlencode(
            sprintf('EGLL,EGBB,EGKR,%s,%s', $noPressureAirfield->code, $noMetarAirfield->code)
        );
        Http::fake(
            [
                $expectedUrl => Http::response(implode("\n", $dataResponse)),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) use ($noPressureAirfield, $noMetarAirfield) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === sprintf('EGLL,EGBB,EGKR,%s,%s', $noPressureAirfield->code, $noMetarAirfield->code);
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
                'raw' => 'EGBB Q0991 A2992',
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
                config(self::URL_CONFIG_KEY) . '?id=' . urlencode('EGLL,EGBB,EGKR') => Http::response('', 500),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        // Check the request
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
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
