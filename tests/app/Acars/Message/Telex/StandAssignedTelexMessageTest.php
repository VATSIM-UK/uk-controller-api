<?php

namespace App\Acars\Message\Telex;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use Illuminate\Support\Str;

class StandAssignedTelexMessageTest extends BaseFunctionalTestCase
{
    private StandAssignment $assignment;
    private StandAssignedTelexMessage $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );
        $this->message = new StandAssignedTelexMessage($this->assignment);
    }

    public function testItHasATarget()
    {
        $this->assertEquals('BAW123', $this->message->getTarget());
    }

    public function testItHasAMessage()
    {
        $expected = <<<END
You have been provisionally assigned stand EGLL/251.

Safe landings.

VATSIM UK
END;

        $expected = Str::replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $this->message->getBody());
    }
}
