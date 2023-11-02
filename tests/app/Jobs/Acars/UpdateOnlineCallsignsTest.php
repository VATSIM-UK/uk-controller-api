<?php

namespace App\Jobs\Acars;

use App\Acars\Provider\HoppieAcarsProvider;
use App\BaseUnitTestCase;
use Illuminate\Queue\Middleware\RateLimited;
use Mockery;

class UpdateOnlineCallsignsTest extends BaseUnitTestCase
{
    public function testItSetsOnlineCallsigns()
    {
        $hoppieProviderMock = Mockery::mock(HoppieAcarsProvider::class);
        $hoppieProviderMock->shouldReceive('setOnlineCallsigns')->once();

        $job = new UpdateOnlineCallsigns();
        $job->handle($hoppieProviderMock);
    }

    public function testItIsRateLimited()
    {
        $job = new UpdateOnlineCallsigns();
        $this->assertEquals([new RateLimited('hoppie')], $job->middleware());
    }
}
