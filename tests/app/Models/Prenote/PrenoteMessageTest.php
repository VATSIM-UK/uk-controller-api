<?php

namespace App\Models\Prenote;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class PrenoteMessageTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
    }

    public function createPrenote(): PrenoteMessage
    {
        return PrenoteMessage::create(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)
            ]
        );
    }

    public function testItFindsActivePrenoteMessages()
    {
        $this->createPrenote();
        $this->assertTrue(PrenoteMessage::activeFor('BAW123')->exists());
    }

    public function testItDoesntCountExpiredPrenotesAsActive()
    {
        $prenote = $this->createPrenote();
        $prenote->expires_at = Carbon::now()->subMinute();
        $prenote->save();

        $this->assertFalse(PrenoteMessage::activeFor('BAW123')->exists());
    }

    public function testItDoesntCountDeletedPrenotesAsActive()
    {
        $this->createPrenote()->delete();
        $this->assertFalse(PrenoteMessage::activeFor('BAW123')->exists());
    }

    public function testItDoesntCountAcknowledgedPrenotesAsActive()
    {
        $prenote = $this->createPrenote();
        $prenote->acknowledged_at = Carbon::now()->subMinute();
        $prenote->save();
        $this->assertFalse(PrenoteMessage::activeFor('BAW123')->exists());
    }

    public function testItFindsPrenotesForTargetController()
    {
        $this->createPrenote();
        $this->assertTrue(PrenoteMessage::target(2)->exists());
    }

    public function testItFindsIgnoresPrenotesForDifferentTargetController()
    {
        $this->createPrenote();
        $this->assertFalse(PrenoteMessage::target(1)->exists());
    }
}
