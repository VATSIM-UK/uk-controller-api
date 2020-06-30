<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;

class OptimiseTablesTest extends BaseFunctionalTestCase
{
    public function testItRuns()
    {
        $this->expectNotToPerformAssertions();
        $this->artisan('tables:optimise');
    }
}
