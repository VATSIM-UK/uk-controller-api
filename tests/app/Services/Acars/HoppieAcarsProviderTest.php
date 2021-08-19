<?php

namespace App\Services\Acars;

use App\BaseFunctionalTestCase;
use App\Exceptions\Acars\AcarsRequestException;
use App\Helpers\Acars\StandAssignedTelexMessage;
use App\Models\Acars\AcarsMessage;
use App\Models\Stand\StandAssignment;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class HoppieAcarsProviderTest extends BaseFunctionalTestCase
{
    private HoppieAcarsProvider $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->app->make(HoppieAcarsProvider::class);
    }

    public function testItThrowsExceptionOnRequestError()
    {
        try {
            Http::fake(
                [
                    config('acars.hoppie.url') => Http::response('ok {}', 500),
                ]
            );

            $this->provider->GetOnlineCallsigns();
        } catch (AcarsRequestException $exception) {
            $loggedMessage = AcarsMessage::find(AcarsMessage::max('id'));
            $this->assertEquals(
                $loggedMessage->message,
                sprintf(
                    'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                    config('acars.hoppie.login_code')
                )
            );
            $this->assertFalse($loggedMessage->successful);
            return;
        }

        self::fail('Expected an AcarsRequestException but did not get one');
    }

    public function testItThrowsExceptionOnBadResponse()
    {
        try {
            Http::fake(
                [
                    config('acars.hoppie.url') => Http::response('notok {}'),
                ]
            );

            $this->provider->GetOnlineCallsigns();
        } catch (AcarsRequestException $exception) {
            $loggedMessage = AcarsMessage::find(AcarsMessage::max('id'));
            $this->assertEquals(
                $loggedMessage->message,
                sprintf(
                    'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                    config('acars.hoppie.login_code')
                )
            );
            $this->assertFalse($loggedMessage->successful);
            return;
        }

        self::fail('Expected an AcarsRequestException but did not get one');
    }

    public function testItLogsOutboundMessages()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok {BAW123 BAW456}'),
            ]
        );

        $this->assertSame(['BAW123', 'BAW456'], $this->provider->GetOnlineCallsigns());

        $loggedMessage = AcarsMessage::find(AcarsMessage::max('id'));
        $this->assertEquals(
            $loggedMessage->message,
            sprintf(
                'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                config('acars.hoppie.login_code')
            )
        );
        $this->assertTrue($loggedMessage->successful);
    }

    public function testItReturnsOnlineCallsigns()
    {
        Http::fake(
            [
                config('acars.hoppie.url') => Http::response('ok {BAW123 BAW456}'),
            ]
        );

        $this->assertSame(['BAW123', 'BAW456'], $this->provider->GetOnlineCallsigns());

        Http::assertSent(function (Request $request) {
            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=ping&to=VATSIMUK&from=VATSIMUK&packet=ALL-CALLSIGNS',
                    config('acars.hoppie.login_code')
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

        $this->assertSame([], $this->provider->GetOnlineCallsigns());
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

        $this->provider->SendTelex($message);

        Http::assertSent(function (Request $request) use ($message) {
            return $request->isForm() &&
                $request->body() === sprintf(
                    'logon=%s&type=telex&to=BAW123&from=VATSIMUK&packet=%s',
                    config('acars.hoppie.login_code'),
                    urlencode($message->getMessage())
                );
        });
    }
}
