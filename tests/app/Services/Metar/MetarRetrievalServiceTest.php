<?php

namespace App\Services\Metar;

use App\BaseUnitTestCase;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetarRetrievalServiceTest extends BaseUnitTestCase
{
    const URL_CONFIG_KEY = 'metar.vatsim_url';

    private MetarRetrievalService $service;

    private int $expectedTimestamp;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MetarRetrievalService::class);
        Carbon::setTestNow(Carbon::parse('2022-01-19 17:17:24'));
        $this->expectedTimestamp = Carbon::now()->timestamp;
    }

    public function testItThrowsExceptionOnBadResponse()
    {
        Http::fake(
            [
                sprintf(
                    '%s?id=%s',
                    config(self::URL_CONFIG_KEY),
                    'EGLL,EGBB,EGKR,' . $this->expectedTimestamp
                ) => Http::response('', 500),
            ]
        );
#
        $this->assertEmpty($this->service->retrieveMetars(collect(['EGLL', 'EGBB', 'EGKR'])));
        $this->assertRequestSent();
    }

    public function testItReturnsMetars()
    {
        $dataResponse = [
            'EGLL Q1001',
            'EGBB Q0991 A2992',
            'EGKR A2992',
            '', // Empty, gets handled
        ];

        Http::fake(
            [
                sprintf(
                    '%s?id=%s',
                    config(self::URL_CONFIG_KEY),
                    'EGLL,EGBB,EGKR,' . $this->expectedTimestamp
                ) => Http::response(
                    implode("\n", $dataResponse)
                ),
            ]
        );

        $metars = $this->service->retrieveMetars(collect(['EGLL', 'EGBB', 'EGKR']));
        $this->assertCount(3, $metars);

        $this->assertEquals(new DownloadedMetar('EGLL Q1001'), $metars['EGLL']);
        $this->assertEquals(new DownloadedMetar('EGBB Q0991 A2992'), $metars['EGBB']);
        $this->assertEquals(new DownloadedMetar('EGKR A2992'), $metars['EGKR']);
        $this->assertRequestSent();
    }

    public function testItSkipsRequestDuringBackoffPeriod()
    {
        Cache::put('metar_backoff_until', Carbon::now()->addMinutes(5), now()->addHours(2));
        Cache::put('metar_error_count', 3, now()->addHours(2));

        Http::fake();

        $result = $this->service->retrieveMetars(collect(['EGLL']));
        
        $this->assertEmpty($result);
        Http::assertNothingSent();
    }

    public function testItRetriesAfterBackoffExpires()
    {
        $dataResponse = ['EGLL Q1001'];

        Cache::put('metar_backoff_until', Carbon::now()->subSecond(), now()->addHours(2));
        Cache::put('metar_error_count', 3, now()->addHours(2));

        Http::fake(
            [
                sprintf(
                    '%s?id=%s',
                    config(self::URL_CONFIG_KEY),
                    'EGLL,EGBB,EGKR,' . $this->expectedTimestamp
                ) => Http::response(
                    implode("\n", $dataResponse)
                ),
            ]
        );

        $result = $this->service->retrieveMetars(collect(['EGLL,EGBB,EGKR,']));
        
        $this->assertCount(1, $result);
        $this->assertRequestSent();
    }

    private function assertRequestSent()
    {
        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Str::startsWith($request->url(), config(self::URL_CONFIG_KEY)) &&
                $request['id'] === 'EGLL,EGBB,EGKR,' . $this->expectedTimestamp;
        });
    }
}
