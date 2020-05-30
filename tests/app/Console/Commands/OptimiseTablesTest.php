<?php

namespace app\Console\Commands;

use App\BaseFunctionalTestCase;

class OptimiseTablesTest extends BaseFunctionalTestCase
{
    public function testItRuns()
    {
        $this->artisan('tables:optimise');
    }
}
