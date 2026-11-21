<?php

namespace App\Console;

use App\BaseUnitTestCase;
use PHPUnit\Framework\Attributes\Test;

class ScheduleHeartbeatTest extends BaseUnitTestCase
{
    #[Test]
    public function it_runs_the_health_heartbeat_every_minute(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('health:schedule-check-heartbeat')
            ->assertSuccessful();
    }
}
