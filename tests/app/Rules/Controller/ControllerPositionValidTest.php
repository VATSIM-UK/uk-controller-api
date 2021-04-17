<?php

namespace App\Rules\Controller;

use App\BaseFunctionalTestCase;

class ControllerPositionValidTest extends BaseFunctionalTestCase
{
    private ControllerPositionValid $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(ControllerPositionValid::class);
    }

    public function testItPassesOnValidPosition()
    {
        $this->assertTrue($this->rule->passes(null, 1));
    }

    public function testItFailsOnInvalidPosition()
    {
        $this->assertFalse($this->rule->passes(null, 12345));
    }
}
