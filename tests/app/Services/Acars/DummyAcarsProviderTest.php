<?php

namespace App\Services\Acars;

use App\BaseFunctionalTestCase;
use App\Helpers\Acars\StandAssignedTelexMessage;
use App\Models\Stand\StandAssignment;

class DummyAcarsProviderTest extends BaseFunctionalTestCase
{
    private DummyAcarsProvider $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->app->make(DummyAcarsProvider::class);
    }

    public function testItReturnsNoCallsigns()
    {
        $this->assertSame([], $this->provider->GetOnlineCallsigns());
    }

    public function testItDoesntSendTelexMessages()
    {
        $this->expectNotToPerformAssertions();
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $message = new StandAssignedTelexMessage($assignment);
        $this->provider->SendTelex($message);
    }
}
