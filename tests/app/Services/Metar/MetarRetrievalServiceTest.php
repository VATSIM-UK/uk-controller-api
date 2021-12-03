<?php

namespace App\Services\Metar;

use App\BaseUnitTestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetarRetrievalServiceTest extends BaseUnitTestCase
{
    const URL_CONFIG_KEY = 'metar.vatsim_url';

    private MetarRetrievalService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MetarRetrievalService::class);
    }

    public function testItThrowsExceptionOnBadResponse()
    {
        Http::fake(
            [
                config(self::URL_CONFIG_KEY) . '?id=' . urlencode('EGLL,EGBB,EGKR') => Http::response('', 500),
            ]
        );

        $this->assertEmpty($this->service->retrieveMetars(collect(['EGLL', 'EGBB', 'EGKR'])));
        $this->assertRequestSent();
    }

    public function testItReturnsMetars()
    {
        $dataResponse = [
            'EGLL Q1001',
            'EGBB Q0991 A2992',
            'EGKR A2992',
        ];

        Http::fake(
            [
                config(self::URL_CONFIG_KEY) . '?id=' . urlencode('EGLL,EGBB,EGKR') => Http::response(
                    implode("\n", $dataResponse),
                    200
                ),
            ]
        );

        $metars = $this->service->retrieveMetars(collect(['EGLL', 'EGBB', 'EGKR']));
        $this->assertCount(3, $metars);

        $this->assertEquals(explode(' ', $dataResponse[0]), $metars['EGLL']->toArray());
        $this->assertEquals(explode(' ', $dataResponse[1]), $metars['EGBB']->toArray());
        $this->assertEquals(explode(' ', $dataResponse[2]), $metars['EGKR']->toArray());
        $this->assertRequestSent();
    }

    private function assertRequestSent()
    {
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === 'EGLL,EGBB,EGKR';
        });
    }
}
