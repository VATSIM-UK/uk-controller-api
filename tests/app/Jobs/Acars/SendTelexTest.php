<?php

namespace App\Jobs\Acars;

use App\Acars\Message\Telex\TelexMessageInterface;
use App\Acars\Provider\HoppieAcarsProvider;
use App\BaseUnitTestCase;
use Mockery;

class SendTelexTest extends BaseUnitTestCase
{
    public function testItSendsATelex()
    {
        $hoppieProviderMock = Mockery::mock(HoppieAcarsProvider::class);
        $telexMessage = Mockery::mock(TelexMessageInterface::class);

        $hoppieProviderMock->shouldReceive('sendTelexMessage')
            ->with($telexMessage)
            ->once();

        $job = new SendTelex($telexMessage);
        $job->handle($hoppieProviderMock);
    }

    public function testItIsRateLimited()
    {
        $job = new UpdateOnlineCallsigns();
        $this->assertEquals([new RateLimited('hoppie')], $job->middleware());
    }
}
