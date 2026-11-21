<?php

namespace App\Providers;

use App\BaseUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Health\Facades\Health;

class HealthServiceProviderTest extends BaseUnitTestCase
{
    #[Test]
    public function it_registers_the_expected_health_checks(): void
    {
        $this->assertSame(
            ['database', 'redis', 'horizon', 'schedule', 'scheduled_tasks'],
            Health::registeredChecks()->map->getName()->all(),
        );
    }
}
