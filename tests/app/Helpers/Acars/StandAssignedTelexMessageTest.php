<?php

namespace App\Helpers\Acars;

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
        $this->message = new StandAssignedTelexMessage('BAW123', $this->assignment);
    }

    public function testItHasATarget()
    {
        $this->assertEquals('BAW123', $this->message->getTarget());
    }

    public function testItHasAMessage()
    {
        $expected = <<<END
VATSIM UK Stand Assignment
--------------------------

You have been provisionally assigned stand 251/EGLL.

This message is non-binding, subject to change at controller discretion and is for planning purposes only.
END;

        $expected = Str::replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $this->message->getMessage());
    }
}
