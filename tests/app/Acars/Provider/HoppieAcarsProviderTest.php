<?php

namespace App\Acars\Provider;

use App\Acars\Message\Telex\TelexMessageInterface;
use App\BaseFunctionalTestCase;
use App\Acars\Exception\AcarsRequestException;
use App\Models\Acars\AcarsMessage;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;

class HoppieAcarsProviderTest extends BaseFunctionalTestCase
{
    private readonly HoppieAcarsProvider $provider;

    private const CACHE_KEY = 'HOPPIE_ACARS_ONLINE_CALLSIGNS';

    private readonly TelexMessageInterface $mockTelex;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->app->make(HoppieAcarsProvider::class);
        Http::preventStrayRequests();
        $this->mockTelex = Mockery::mock(TelexMessageInterface::class);
    }

    public function tearDown(): void
    {
        Cache::forget(self::CACHE_KEY);
        parent::tearDown();
    }

    public function testItThrowsExceptionOnRequestError()
    {
        Cache::set(self::CACHE_KEY, collect(['BAW123', 'BAW456']), 300);
        try {
            Http::fake(
                [
                    config('acars.hoppie.url') => Http::response('ok {}', 500),
                ]
            );

            $this->mockTelex->shouldReceive('getTarget')->andReturn('BAW123');
            $this->mockTelex->shouldReceive('getBody')->andReturn('TEST');
            $this->provider->sendTelex($this->mockTelex);
        } catch (AcarsRequestException) {
            $loggedMessage = AcarsMessage::find(AcarsMessage::max('id'));
            $this->assertEquals(
                $loggedMessage->message,
                'type=telex&to=BAW123&from=VATSIMUK&packet=TEST'
            );
            $this->assertFalse($loggedMessage->successful);
            return;
        }

        self::fail('Expected an AcarsRequestException but did not get one');
    }

    public function testItThrowsExceptionOnBadResponse()
    {
        Cache::set(self::CACHE_KEY, collect(['BAW123', 'BAW456']), 300);
        try {
            Http::fake(
                [
                    config('acars.hoppie.url') => Http::response('notok {}'),
                ]
            );

            $this->mockTelex->shouldReceive('getTarget')->andReturn('BAW123');
            $this->mockTelex->shouldReceive('getBody')->andReturn('TEST');
            $this->provider->sendTelex($this->mockTelex);
        } catch (AcarsRequestException $exception) {
            $loggedMessage = AcarsMessage::find(AcarsMessage::max('id'));
            $this->assertEquals(
                $loggedMessage->message,
                'type=telex&to=BAW123&from=VATSIMUK&packet=TEST'
            );
            $this->assertFalse($loggedMessage->successful);
            return;
        }

        self::fail('Expected an AcarsRequestException but did not get one');
    }

    public function testItGetsOnlineCallsignsIfNotCached()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::sequence()
                    ->push('ok {BAW123 BAW456}')
                    ->push('ok')
            ]
        );

        $this->mockTelex->shouldReceive('getTarget')->andReturn('BAW123');
        $this->mockTelex->shouldReceive('getBody')->andReturn('TEST');
        $this->provider->sendTelex($this->mockTelex);

        Http::assertSent(function (Request $request) {
            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                    config('acars.hoppie.login_code')
                );
        });

        Http::assertSent(function (Request $request) {
            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=telex&to=BAW123&from=VATSIMUK&packet=TEST',
                    config('acars.hoppie.login_code')
                );
        });

        $this->assertEquals(collect(['BAW123', 'BAW456']), Cache::get(self::CACHE_KEY));
    }

    public function testItDoesntSendATelexIfAircraftNotOnline()
    {
        Cache::set(self::CACHE_KEY, collect(['BAW456']), 300);
        Http::fake();

        $this->mockTelex->shouldReceive('getTarget')->andReturn('BAW123');
        $this->mockTelex->shouldReceive('getBody')->andReturn('TEST');
        $this->provider->sendTelex($this->mockTelex);

        Http::assertNothingSent();
    }

    public function testItSendsATelex()
    {
        Cache::set(self::CACHE_KEY, collect(['BAW123', 'BAW456']), 300);
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok'),
            ]
        );

        $this->mockTelex->shouldReceive('getTarget')->andReturn('BAW123');
        $this->mockTelex->shouldReceive('getBody')->andReturn('TEST');
        $this->provider->sendTelex($this->mockTelex);

        Http::assertSent(function (Request $request) {
            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=telex&to=BAW123&from=VATSIMUK&packet=TEST',
                    config('acars.hoppie.login_code')
                );
        });
    }
}
