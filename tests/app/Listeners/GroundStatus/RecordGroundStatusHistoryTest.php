<?php

namespace App\Listeners\GroundStatus;

use App\BaseFunctionalTestCase;
use App\Events\GroundStatusAssignedEvent;
use Carbon\Carbon;
use TestingUtils\Traits\WithSeedUsers;

class RecordGroundStatusHistoryTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
    }

    public function testItRecordsStatusHistoryAsUser()
    {
        $this->actingAs($this->activeUser());
        $this->assertTrue(
            (new RecordGroundStatusHistory())->handle(new GroundStatusAssignedEvent('BAW123', 1))
        );
        $this->assertDatabaseHas(
            'ground_status_history',
            [
                'callsign' => 'BAW123',
                'ground_status_id' => 1,
                'user_id' => $this->activeUser()->id,
                'assigned_at' => Carbon::now(),
            ]
        );
    }

    public function testItRecordsStatusHistoryAsSystem()
    {
        $this->assertTrue(
            (new RecordGroundStatusHistory())->handle(new GroundStatusAssignedEvent('BAW123', 1))
        );
        $this->assertDatabaseHas(
            'ground_status_history',
            [
                'callsign' => 'BAW123',
                'ground_status_id' => 1,
                'user_id' => null,
                'assigned_at' => Carbon::now(),
            ]
        );
    }
}
