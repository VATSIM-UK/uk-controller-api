<?php
namespace App\Services;

use App\BaseFunctionalTestCase;
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

    public function testItUpdatesAllMetars()
    {
        Metar::create(['airfield_id' => 1, 'metar_string' => 'bla']);

        $dataResponse = [
            'EGLL ABC DEF',
            'EGKR GHI JKL',
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
                'EGLL ABC DEF'
            ]
        );

        $this->assertDatabaseHas(
            'metars',
            [
                'airfield_id' => 3,
                'EGKR GHI JKL'
            ]
        );

        $this->assertDatabaseMissing(
            'metars',
            [
                'airfield_id' => 2,
            ]
        );
    }

    public function testItHandlesBadResponsesGracefully()
    {
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
