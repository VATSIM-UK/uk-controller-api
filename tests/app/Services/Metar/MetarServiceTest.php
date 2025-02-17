<?php

namespace App\Services\Metar;

use App\BaseFunctionalTestCase;
use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use App\Services\Metar\Parser\MetarParser;
use App\Services\Metar\Parser\ObservationTimeParser;
use App\Services\Metar\Parser\PressureParser;
use App\Services\Metar\Parser\VisibilityParser;
use App\Services\Metar\Parser\WindParser;
use App\Services\Metar\Parser\WindVariationParser;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetarServiceTest extends BaseFunctionalTestCase
{
    const URL_CONFIG_KEY = 'metar.vatsim_url';

    private MetarService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MetarService::class);
        Event::fake();
        Carbon::setTestNow(Carbon::now());
    }

    public function testItParsesMetars()
    {
        Metar::create(['airfield_id' => 1, 'raw' => 'bla', 'parsed' => []]);
        $noPressureAirfield = Airfield::factory()->create();
        $noMetarAirfield = Airfield::factory()->create();

        $dataResponse = [
            'EGLL Q1001',
            // Will pull through the QNH as 1001
            'EGBB Q0991 A2992',
            // Will pull through the QNH as 991
            'EGKR A2992', // Will pull through altimeter and convert it to QNH,
            $noPressureAirfield->code, // Wont pull through a QNH as there is none.
        ];


        $expectedUrl = config(self::URL_CONFIG_KEY) . '?id=' .
            sprintf(
                'EGLL,EGBB,EGKR,%s,%s,%s',
                $noPressureAirfield->code,
                $noMetarAirfield->code,
                Carbon::now()->timestamp
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
                $request['id'] === sprintf(
                    'EGLL,EGBB,EGKR,%s,%s,%s',
                    $noPressureAirfield->code,
                    $noMetarAirfield->code,
                    Carbon::now()->timestamp
                );
        });

        Event::assertDispatched(MetarsUpdatedEvent::class);

        // Check the metars are in the database
        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 1,
                'raw' => 'EGLL Q1001',
                'parsed->qnh' => 1001,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 2,
                'raw' => 'EGBB Q0991 A2992',
                'parsed->qnh' => 991,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 3,
                'raw' => 'EGKR A2992',
                'parsed->qnh' => 1013,
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => $noPressureAirfield->id,
                'raw' => $noPressureAirfield->code,
                'parsed->qnh' => null,
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
        Http::fake(
            [
                config(self::URL_CONFIG_KEY) . '?id=' .
                'EGLL,EGBB,EGKR,' . Carbon::now()->timestamp
                => Http::response('', 500),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === 'EGLL,EGBB,EGKR,' . Carbon::now()->timestamp;
        });

        $this->assertDatabaseCount(
            'metars',
            0
        );
        Event::assertNotDispatched(MetarsUpdatedEvent::class);
    }

    public function testItHasParsers()
    {
        $expected = [
            ObservationTimeParser::class,
            PressureParser::class,
            WindParser::class,
            WindVariationParser::class,
            VisibilityParser::class,
        ];

        $this->assertEquals(
            $expected,
            $this->service->getParsers()->map(function (MetarParser $parser) {
                return get_class($parser);
            })->toArray()
        );
    }

    public function testItTriggersEventsOnlyForUpdatedMetars()
    {
        $metarOne = Metar::factory()->create();
        $metarTwo = Metar::factory()->create();

        $dataResponse = [
            $metarOne->raw, // This hasn't changed, so shouldn't come up in event
            $metarTwo->raw . ' hi', // Has changed, will pull through
        ];

        $expectedUrl = config(self::URL_CONFIG_KEY) . '?id=' .
            sprintf(
                'EGLL,EGBB,EGKR,%s,%s,%s',
                $metarOne->airfield->code,
                $metarTwo->airfield->code,
                Carbon::now()->timestamp
            );
        Http::fake(
            [
                $expectedUrl => Http::response(implode("\n", $dataResponse)),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) use ($metarOne, $metarTwo) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === sprintf(
                    'EGLL,EGBB,EGKR,%s,%s,%s',
                    $metarOne->airfield->code,
                    $metarTwo->airfield->code,
                    Carbon::now()->timestamp
                );
        });

        Event::assertDispatched(MetarsUpdatedEvent::class, function (MetarsUpdatedEvent $event) use ($metarTwo) {
            return $event->getMetars()->toArray() == collect(
                    [Metar::with('airfield')->findOrFail($metarTwo->id)]
                )->toArray();
        });
    }

    public function testItDoesntTriggerEventIfMetarsDontChange()
    {
        $metarOne = Metar::factory()->create();
        $metarTwo = Metar::factory()->create();

        $dataResponse = [
            $metarOne->raw,
            $metarTwo->raw,
        ];

        $expectedUrl = config(self::URL_CONFIG_KEY) . '?id=' .
            sprintf(
                'EGLL,EGBB,EGKR,%s,%s,%s',
                $metarOne->airfield->code,
                $metarTwo->airfield->code,
                Carbon::now()->timestamp
            );
        Http::fake(
            [
                $expectedUrl => Http::response(implode("\n", $dataResponse)),
            ]
        );

        $this->service->updateAllMetars();

        // Check the request
        Http::assertSent(function (Request $request) use ($metarOne, $metarTwo) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === sprintf(
                    'EGLL,EGBB,EGKR,%s,%s,%s',
                    $metarOne->airfield->code,
                    $metarTwo->airfield->code,
                    Carbon::now()->timestamp
                );
        });

        Event::assertNotDispatched(MetarsUpdatedEvent::class);
    }
}
