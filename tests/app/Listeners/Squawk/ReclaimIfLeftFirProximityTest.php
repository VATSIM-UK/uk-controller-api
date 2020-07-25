<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;

class ReclaimIfLeftProximityTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->app->make(ReclaimIfLeftFirProximity::class);
    }
}
