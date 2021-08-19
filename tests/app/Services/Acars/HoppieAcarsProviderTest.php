<?php

namespace App\Services\Acars;

use App\BaseFunctionalTestCase;
use App\Exceptions\Acars\AcarsRequestException;
use App\Helpers\Acars\StandAssignedTelexMessage;
use App\Models\Stand\StandAssignment;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class HoppieAcarsProviderTest extends BaseFunctionalTestCase
{
    private HoppieAcarsProvider $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(HoppieAcarsProvider::class);
    }

    public function testItThrowsExceptionOnRequestError()
    {
        $this->expectException(AcarsRequestException::class);
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok {}', 500),
            ]
        );

        $this->service->GetOnlineCallsigns();
    }

    public function testItThrowsExceptionOnBadResponse()
    {
        $this->expectException(AcarsRequestException::class);
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('notok {}'),
            ]
        );

        $this->service->GetOnlineCallsigns();
    }

    public function testItReturnsOnlineCallsigns()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok {BAW123 BAW456}'),
            ]
        );

        $this->assertSame(['BAW123', 'BAW456'], $this->service->GetOnlineCallsigns());

        Http::assertSent(function (Request $request) {
            $loginCode = config('acars.hoppie.login_code');

            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                    $loginCode
                );
        });
    }

    public function testItReturnsNoOnlineCallsigns()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok {}'),
            ]
        );

        $this->assertSame([], $this->service->GetOnlineCallsigns());
    }

    public function testItSendsATelex()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok'),
            ]
        );

        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $message = new StandAssignedTelexMessage('BAW123', $assignment);

        $this->service->SendTelex($message);

        Http::assertSent(function (Request $request) use ($message) {
            $loginCode = config('acars.hoppie.login_code');

            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=telex&to=BAW123&from=VATSIMUK&packet=%s',
                    $loginCode,
                    urlencode($message->getMessage())
                );
        });
    }
}
